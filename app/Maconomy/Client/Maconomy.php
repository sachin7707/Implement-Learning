<?php

namespace App\Maconomy\Client;

use App\Maconomy\Collection\CourseCollection;
use App\Maconomy\Model\Course;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Maconomy implements ClientAbstract, LoggerAwareInterface
{
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
        return $this->parseCourses(
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
        return new CourseCollection([
            $this->parseCourse($this->callWebservice("course/$id"))
        ]);
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

    /**
     * Calls maconomy to get the number of free seats on a given course
     * @param string $maconomyId the course id
     * @return int the number of available seats
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAvailableSeats(string $maconomyId): int
    {
        $data = $this->callWebservice("courselight/$maconomyId");
        return (int)$data->freeSeatsField;
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
     * Parses the course in the given response, returning a nice model instead
     * @param mixed $data the data from the webservice
     * @return Course
     */
    private function parseCourse($data): Course
    {
        $course = new Course();
        $course->maconomyId = $data->courseNumberField;
        $course->name = $data->courseNameField;
        $course->price = $data->priceField;
        $course->maxParticipants = $data->maxParticipantsField;
        $course->minParticipants = $data->minParticipantsField;
        $course->startTime = new \DateTime($data->startingDateField, new \DateTimeZone('GMT'));
        $course->endTime = new \DateTime($data->endingDateField, new \DateTimeZone('GMT'));
        $course->language = $data->weblanguageField;
        $course->venueId = $data->venueField;
        $course->venueName = $data->venuenameField;
        $course->seatsAvailable = $data->freeSeatsField;
        $course->currentParticipants = $data->enrolledField;

        return $course;
    }

    /**
     * Parses the given webservice data, containing a list of courses and returns a nice CourseCollection
     * @param array $data the list of courses from the webservice
     * @return CourseCollection
     */
    private function parseCourses(array $data)
    {
        $courses = [];

        // parsing the courses
        foreach ($data as $courseData) {
            $courses[] = $this->parseCourse($courseData);
        }

        return new CourseCollection($courses);
    }
}
