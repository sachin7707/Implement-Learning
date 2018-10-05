<?php

namespace App\Maconomy\Service;

use App\Course;
use App\Order;
use Illuminate\Support\Facades\DB;

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
     */
    public function reserveSeats(Order $order, int $requiredSeats): bool
    {
        $this->courseService->updateSeatsAvailable($order->course);

        // fetching the number of seats available on the course (without the currently reserved seats)
        $availableSeats = $this->getAvailableSeatsOnCourse($order);

        // if we can, reserve the seats!
        if ($availableSeats >= $requiredSeats) {
            $order->seats = $requiredSeats;
            $order->save();

            return true;
        }

        return false;
    }

    /**
     * Fetches the number of available seats, on a course, on the given order.
     * @param Order $order the current order. (NOTE: the course_id is used from the order)
     * @return int the number of seats available on a given course
     */
    public function getAvailableSeatsOnCourse(Order $order): int
    {
        $course = Course::find($order->course_id);

        $reservedSeats = DB::table('orders')
            ->select(Db::raw('sum(seats) as seat_count'))
            ->where('state', '!=', Order::STATE_CONFIRMED)
            ->where('course_id', $course->id)
            // excluding current order
            ->where('id', '!=', $order->id)
            ->value('seat_count');

        if (empty($reservedSeats)) {
            return 0;
        }

        // no seats available? return 0
        if ($course->seats_available <= $reservedSeats) {
            return 0;
        }

        // return the number of available seats, when reserved seats are removed
        return $course->seats_available - $reservedSeats;
    }
}
