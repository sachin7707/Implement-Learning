<?php

namespace App\Maconomy\Client;

use App\Maconomy\Client\AbstractFactory\ParserFactory;
use App\Maconomy\Client\Exception\NoOrderException;
use App\Maconomy\Client\Order\Participant;
use App\Maconomy\Collection\CourseCollection;
use App\Maconomy\Collection\CourseTypeCollection;
use App\Maconomy\Token;
use GuzzleHttp\Client;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Maconomy implements ClientAbstract
{
    /** @var OrderAdapter $order current order to sync with maconomy */
    private $order;
    /** @var Client */
    private $client;
    /** @var string $baseUrl */
    private $baseUrl;

    /**
     * Crm constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        // saving soap url and location
        $this->baseUrl = $baseUrl;
    }

    /**
     * Fetches the actual soap client to use.
     * @return Client
     */
    private function getClient(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client(['base_uri' => $this->baseUrl]);

        return $this->client;
    }

    /**
     * Fetches the course dates from maconomy
     * @return CourseCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCourses()
    {
        $parser = $this->getParserFactory()->createCourseParser();
        return $parser->parseCollection(
            $this->callWebservice("api/course")
        );
    }

    /**
     * Fetches a single course from maconomy
     * @param string $id the id of the course
     * @return CourseCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCourse(string $id)
    {
        $parser = $this->getParserFactory()->createCourseParser();
        return new CourseCollection([
            $parser->parse($this->callWebservice("api/course/$id"))
        ]);
    }

    /**
     * Fetches the course dates from maconomy
     * @return CourseTypeCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCourseTypes()
    {
        $parser = $this->getParserFactory()->createCourseTypeParser();
        return $parser->parseCollection(
            $this->callWebservice("api/maincourse")
        );
    }

    /**
     * Sets the order, we are syncing to maconomy
     * @param OrderAdapter $order
     */
    public function setOrder(OrderAdapter $order)
    {
        $this->order = $order;
    }

    /**
     * Removes the current order
     */
    private function clearOrder()
    {
        $this->order = null;
    }

    /**
     * Checks if the given response is valid
     * @return Response the response sent from the server
     * @throws NoOrderException
     */
    public function orderCreate(): Response
    {
        if ($this->order === null) {
            throw new NoOrderException('No order was set');
        }

        // TODO: Implement orderCreate() method.

        // runs though the participants, sending them to the webservice one by one
        /** @var Participant $participant */
        foreach ($this->order->getParticipants() as $participant) {
            // sends the data to the webservice
            $response = $this->callWebservice('api/webparticipant', 'post', $participant->getData());

            // TODO: update the participant, with the new instancekey (maconomy_id) in the database, using $response
        }

        $this->order->markAsSynced();

        $this->clearOrder();
    }

    /**
     * Updates the given order
     * @param \App\Order $order the order to update in maconomy
     * @return Response
     * @throws NoOrderException
     */
    public function orderUpdate(): Response
    {
        if ($this->order === null) {
            throw new NoOrderException('No order was set');
        }

        // TODO: Implement orderUpdate() method.


        $this->clearOrder();
    }

    /**
     * Deletes the order with the given id on the server
     * @param int $id the order id to delete
     * @return Response
     */
    public function orderDelete(int $id): Response
    {
        // TODO: Implement orderDelete() method.

    }

    /**
     * Calls maconomy to get the number of free seats on a given course
     * @param string $maconomyId the course id
     * @return int the number of available seats
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEnrolledSeats(string $maconomyId): int
    {
        $data = $this->callWebservice("api/course/$maconomyId");
        return (int)$data->enrolledField;
    }

    /**
     * Calls the webservice, using the given uri part, to append to the baseurl.
     *
     * @param string $uri the last part of the url to call
     * @param string $method the method to use (get, post, put etc)
     * @param array $options the data to send
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callWebservice(string $uri, string $method = 'get', array $options = [])
    {
        $token = $this->getToken();

        // adding authentication headers
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $token->access_token,
        ]);

        return $this->callWebserviceRaw($uri, $method, $options);
    }

    /**
     * Calls the webservice without adding an auth token.
     *
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callWebserviceRaw(string $uri, string $method, array $options)
    {
        return json_decode((string)$this->getClient()->request($method, $uri, $options)->getBody());
    }

    /**
     * @return ParserFactory
     */
    private function getParserFactory(): ParserFactory
    {
        return new ParserFactory();
    }

    /**
     * Fetches a valid token, that can be used when calling the webservice
     *
     * @return Token
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getToken(): Token
    {
        $now = new \DateTime('now', new \DateTimeZone('GMT'));
        // fetches an existing token (if any)
        $token = Token::where('expires', '>', $now->format('Y-m-d H:i:s'))
            ->orderBy('id', 'DESC')
            ->first();

        if (! empty($token)) {
            return $token;
        }

        // no token found, create a new token
        return $this->generateToken();
    }

    /**
     * Generates a new token and saves the data to the database.
     *
     * @return Token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function generateToken(): Token
    {
        // fetches a new token from the webservice.
        $tokenData = $this->callWebserviceRaw('token', 'get', [
            'form_params' => [
                'grant_type' => 'password',
                'userName' => env('MACONOMY_USERNAME'),
                'password' => env('MACONOMY_PASSWORD')
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        // parsing dates from the webservice
        $expires = new \DateTime($tokenData->{'.expires'});
        // removing 5hours from the "max" alive time, just to avoid any expiring issues here :)
        $expires->sub(new \DateInterval('PT5H'));
        $issued = new \DateTime($tokenData->{'.issued'});

        $token = new Token([
            'access_token' => $tokenData->access_token,
            'token_type' => $tokenData->token_type,
            'expires_in' => (int)$tokenData->expires_in,
            'username' => $tokenData->userName,
            'issued' => $issued->format('Y-m-d H:i:s'),
            'expires' => $expires->format('Y-m-d H:i:s'),
        ]);

        // saves and refreshes the newly created token
        $token->save();
        $token->refresh();

        return $token;
    }
}
