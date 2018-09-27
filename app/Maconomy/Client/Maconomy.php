<?php

namespace App\Maconomy\Client;

use App\Maconomy\Collection\CourseCollection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Maconomy implements SoapClient, LoggerAwareInterface
{
    /** @var \SoapClient $client */
    private $client;
    /** @var string $url */
    private $url;
    /** @var string $location */
    private $location;
    /** @var LoggerInterface  */
    private $logger;

    /**
     * Crm constructor.
     * @param string $url the wsdl url of the webservice to connect to
     * @param string $location the location
     * @param LoggerInterface $logger
     */
    public function __construct(string $url, string $location)
    {
        // saving soap url and location
        $this->url = $url;
        $this->location = $location;
    }

    /**
     * Fetches the actual soap client to use.
     * @return \SoapClient
     * @throws \ErrorException if the client cannot be initialized
     */
    private function getClient(): \SoapClient
    {
        if (null !== $this->client) {
            return $this->client;
        }

        $this->client = new \SoapClient(
            $this->url,
            [
                'cache_wsdl' => WSDL_CACHE_NONE,
                'soap_version' => SOAP_1_1,
                'exceptions' => false,
                'trace' => false,
            ]
        );
        $this->client->__setLocation($this->location);

        return $this->client;
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Fetches the course dates from maconomy
     * @return CourseCollection
     */
    public function getCourses()
    {
        // TODO: get the courses from maconomy
        return new CourseCollection([]);
    }

    /**
     * Fetches a single course from maconomy
     * @param string $id the id of the course
     * @return CourseCollection
     */
    public function getCourse(string $id)
    {
        // TODO: get the course from maconomy
        return new CourseCollection([]);
    }

    /**
     * Checks if the given response is valid
     * @return Response the response sent from the server
     */
    public function orderCreate(): Response
    {
        // TODO: Implement orderCreate() method.
    }

    /**
     * Updates the given order
     * @return Response
     */
    public function orderUpdate(Order $data): Response
    {
        // TODO: Implement orderUpdate() method.
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
}