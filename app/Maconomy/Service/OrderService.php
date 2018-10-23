<?php

namespace App\Maconomy\Service;

use App\Course;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-10-02
 */
class OrderService
{
    /** @var CourseService */
    private $courseService;

    /**
     * OrderService constructor.
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Reserves the given number of seats, on the given order.
     * @param Order $order
     * @param int $requiredSeats
     * @return bool false if the seats could not be reserved, else true
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reserveSeats(Order $order, int $requiredSeats): bool
    {
        $course = $order->course;
        // updates the seats available from maconomy
        $this->courseService->updateSeatsAvailable($course);

        // fetching the number of seats available on the course (without the currently reserved seats)
        $availableSeats = $course->getAvailableSeats($order);

        // if we can, reserve the seats!
        if ($availableSeats >= $requiredSeats) {
            $order->seats = $requiredSeats;
            $order->save();

            return true;
        }

        return false;
    }

    /**
     * Checks if the given course is still signupable, by looking at the signup deadline
     * @param Course $course the course to check
     * @return bool
     */
    public function isBeforeDeadline(Course $course): bool
    {
        // fetching "now"
        $now = new \DateTime('now', new \DateTimeZone('GMT'));

        return $course->deadline > $now;
    }

    /**
     * Closes the given order, marking it as ready for sync with maconomy.
     * @param Order $order the order to close and save participants and company info on.
     * @param array $participants the list of participants to add to the order
     * @param array $company the company information
     */
    public function closeOrder(Order $order, array $participants, array $company): void
    {
        // TODO: save participants locally
        // TODO: save company locally
        $order->state = Order::STATE_CLOSED;
        $order->save();
    }
}
