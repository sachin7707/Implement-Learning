<?php

namespace App\Maconomy\Service;

use App\Course;
use App\Maconomy\Client\Maconomy;
use Illuminate\Support\Facades\Log;

/**
 * @author jimmiw
 * @since 2018-10-02
 */
class CourseService
{
    /** @var Maconomy */
    private $client;

    /**
     * Course constructor.
     * @param Maconomy $client
     */
    public function __construct(Maconomy $client)
    {
        $this->client = $client;
    }

    /**
     * Checks if we have any seats available on the given course, using the number of seats required, to ensure we have
     * enough.
     * @param Course $course the course to get the number of seats for
     * @param bool $shouldResync should we call maconomy for this information?
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSeatsAvailable(Course $course, bool $shouldResync = true): void
    {
        // fetches the number of seats, from the webservice
        $availableSeats = $this->getSeatsAvailable($course, $shouldResync);

        // updates the number of seats available, if needed
        if ($course->seats_available !== $availableSeats) {
            $course->seats_available = $availableSeats;
            // updating our course in the database (this is used when syncing to WP)
            $course->save();
        }
    }

    /**
     * Fetches the number of seats available on a given course
     * @param Course $course
     * @param bool $shouldResync should we call maconomy for this information?
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSeatsAvailable(Course $course, bool $shouldResync = true): int
    {
        $seatsUsed = $course->participants_current;

        if ($shouldResync) {
            try {
                $seatsUsed = $this->client->getEnrolledSeats($course->maconomy_id);
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                Log::channel('sentry')->error($e->getMessage(), $e->getTrace());
            }
        }

        return $course->participants_max - $seatsUsed;
    }
}