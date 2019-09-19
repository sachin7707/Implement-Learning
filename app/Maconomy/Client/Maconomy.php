<?php

namespace App\Maconomy\Client;

use App\Maconomy\Client\AbstractFactory\ParserFactory;
use App\Maconomy\Client\Exception\NoOrderException;
use App\Maconomy\Client\Exception\Order\ParticipantException;
use App\Maconomy\Client\Models\Course\Participant as ParticipantCourse;
use App\Maconomy\Client\Order\ParticipantAdapter;
use App\Maconomy\Collection\CourseCollection;
use App\Maconomy\Collection\CourseTypeCollection;
use App\Maconomy\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Maconomy implements ClientAbstract
{
    const TIMEOUT_IN_SECONDS = 30;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ParticipantException
     */
    public function orderCreate(): Response
    {
        if ($this->order === null) {
            throw new NoOrderException('No order was set');
        }

        // runs though the participants, sending them to the webservice one by one
        /** @var ParticipantAdapter $orderParticipant */
        foreach ($this->order->getParticipants() as $orderParticipant) {
            // sets the participant url
            $url = 'api/webparticipant';
            $method = 'post';

            // checking if the participant should be updated
            if ($orderParticipant->hasMaconomyId()) {
                $url .= '/' . $orderParticipant->getMaconomyId();
                $method = 'put';
            }

            $response = '';

            try {
                $this->getToken();
                // sends the data to the webservice
                $response = $this->callWebservice(
                    $url,
                    $method,
                    ['json' => $orderParticipant->getData()]
                );

                // if the response is an array, assume all is well, since we are not getting an error response
                if (! empty($response[0]) && ! $orderParticipant->hasMaconomyId()) {
                    // fetches the participant, so we can update the maconomy_id
                    $participant = \App\Participant::find($orderParticipant->getId());
                    $participant->maconomy()->save(
                        new \App\Participant\Maconomy([
                            'course_id' => $orderParticipant->getCourseId(),
                            'maconomy_id' => $response[0]->instancekeyField,
                        ])
                    );
//                    $participant->maconomy_id = $response[0]->instancekeyField;
                    $participant->save();
                }
            } catch (RequestException $e) {
                throw new ParticipantException(
                    'Could not save participant using ' . $method,
                    array_merge($orderParticipant->getData(), [
                        'response' => $response
                    ]),
                    0,
                    $e
                );
            }
        }

        $this->order->markAsSynced();

        $this->clearOrder();

        return new Order\Response('Order synced');
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

        return (int)($data->enrolledField ?? $data->enrolled);
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
        // adding a default timeout, since sometimes the maconomy service is crashed or very slow
        // and we cannot have the jobs wait forever - ILI-767
        if (is_array($options) && ! isset($options['timeout'])) {
            $options['timeout'] = self::TIMEOUT_IN_SECONDS;
        }

        $response = $this->getClient()->request($method, $uri, $options);
        return json_decode((string)$response->getBody());
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

    /**
     * Fetches the participants on the given course, on the maconomy server.
     * @param string $maconomyId the course maconomy id
     * @return array the list of participants
     * @throws GuzzleException
     */
    public function getParticipantsOnCourse(string $maconomyId): array
    {
        $data = $this->callWebservice("api/participant/$maconomyId");

        $participants = [];

        foreach ($data as $row) {
            $participant = new ParticipantCourse(
                $row->personName,
                $row->email,
                $row->companyName,
                $row->phone ?? '',
                $row->title ?? ''
            );

            // sets the participant to be on the waiting list
            if ($row->signedUp !== 1) {
                $participant->setIsOnWaitingList();
            }

            $participants[] = $participant;
        }

        return $participants;
    }
}
