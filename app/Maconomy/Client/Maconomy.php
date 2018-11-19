<?php

namespace App\Maconomy\Client;

use App\Maconomy\Client\AbstractFactory\ParserFactory;
use App\Maconomy\Client\Exception\NoOrderException;
use App\Maconomy\Collection\CourseCollection;
use App\Maconomy\Collection\CourseTypeCollection;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Maconomy implements ClientAbstract, LoggerAwareInterface
{
    /** @var \App\Order $order current order to sync with maconomy */
    private $order;
    /** @var Client */
    private $client;
    /** @var string $baseUrl */
    private $baseUrl;
    /** @var LoggerInterface  */
    private $logger;

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
     * Sets a logger instance on the object.
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
            $this->callWebservice("course")
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
            $parser->parse($this->callWebservice("course/$id"))
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
            $this->callWebservice("maincourse")
        );
    }

    /**
     * Sets the order, we are syncing to maconomy
     * @param \App\Order $order
     */
    public function setOrder(\App\Order $order)
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
        if (empty($this->order)) {
            throw new NoOrderException('No order was set');
        }
        // TODO: Implement orderCreate() method.



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
        if (empty($this->order)) {
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
        $data = $this->callWebservice("course/$maconomyId");
        return (int)$data->enrolledField;
    }

    /**
     * Calls the webservice, using the given uri part, to append to the baseurl.
     * @param string $uri the last part of the url to call
     * @param string $method the method to use (get, post, put etc)
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callWebservice(string $uri, string $method = 'get')
    {
        return json_decode((string)$this->getClient()->request($method, $uri)->getBody());
    }

    /**
     * @return ParserFactory
     */
    private function getParserFactory(): ParserFactory
    {
        return new ParserFactory();
    }
}
