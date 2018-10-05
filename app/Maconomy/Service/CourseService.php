<?php

namespace App\Maconomy\Service;

use App\Course;
use App\Maconomy\Client\Maconomy;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-10-02
 */
class CourseService
{
    /** @var Maconomy  */
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
     */
    public function updateSeatsAvailable(Course $course): void
    {
        // fetches the number of seats, from the webservice
        $availableSeats = $this->getSeatsAvailable($course);

        // updates the number of seats available, if needed
        if ($course->seats_available !== $availableSeats) {
            $course->seats_available = $availableSeats;
            // updating our course in the database (this is used when syncing to WP)
            $course->save();
        }
    }

    /**
     * Fetches the "locally stored" number of seats taken
     * @param Course $course
     * @return int the number of "local" seats taken
     */
    public function getNumberOfSeatsTaken(Course $course): int
    {
        $seatsTaken = 0;
        /** @var Order $order */
        foreach ($course->orders as $order) {
            $seatsTaken += (int)$order->seats;
        }

        return $seatsTaken;
    }

    /**
     * Fetches the number of seats available on a given course
     * @param Course $course
     * @return int
     */
    public function getSeatsAvailable(Course $course): int
    {
        return $this->client->getAvailableSeats($course->maconomy_id);
    }
}