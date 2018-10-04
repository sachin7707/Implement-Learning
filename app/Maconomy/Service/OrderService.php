<?php

namespace App\Maconomy\Service;

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
     */
    public function reserveSeats(Order $order, int $requiredSeats): bool
    {
        // TODO: reduce the number of required seats, by the number we already have reserved.
        if ($this->courseService->hasSeatsAvailable($order->course, $requiredSeats)) {
            $order->seats = $requiredSeats;
            $order->save();

            return true;
        }

        return false;
    }
}