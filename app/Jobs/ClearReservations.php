<?php

namespace App\Jobs;

use App\Events\CoursesSyncedEvent;
use App\Order;
use Illuminate\Support\Facades\Event;

/**
 * A simple cleanup job, to unreserve seats on orders, that have not been active for more than 15 minutes.
 * @author jimmiw
 * @since 2018-10-05
 */
class ClearReservations extends Job
{
    public function handle()
    {
        // fetching the max age, an order can have since last update (defaults to 30minutes)
        $maxAge = env('ORDER_CLEAR_RESERVATIONS_AGE', '30');

        $now = new \DateTime('now', new \DateTimeZone('GMT'));
        $now->sub(new \DateInterval('PT'.$maxAge.'M'));

        // fetches the orders, that has not been updated in XX minutes, and removes the reserved seats.
        $orders = Order::where('state', '!=', Order::STATE_CONFIRMED)
            ->where('updated_at', '<', $now->format('Y-m-d H:i:s'))
            ->where('seats', '>', 0)
            ->get();

        /** @var Order $order */
        foreach ($orders as $order) {
            $order->seats = 0;
            $order->save();

            foreach ($order->courses() as $course) {
                // telling wordpress to update the given course, since we have changed the number of seats available
                Event::dispatch(new CoursesSyncedEvent($course->maconomyId));
            }
        }
    }
}
