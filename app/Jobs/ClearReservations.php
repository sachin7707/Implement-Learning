<?php

namespace App\Jobs;

use App\Order;

/**
 * A simple cleanup job, to unreserve seats on orders, that have not been active for more than 15 minutes.
 * @author jimmiw
 * @since 2018-10-05
 */
class ClearReservations extends Job
{
    public function handle()
    {
        // fetches "now", 15 minutes ago
        $now = new \DateTime('now', new \DateTimeZone('GMT'));
        $now->sub(new \DateInterval('PT30M'));

        // fetches the orders, that has not been updated in 15 minutes, and removes the taken seats from them.
        $orders = Order::where('state', '!=', Order::STATE_CONFIRMED)
            ->where('updated_at', '<', $now->format('Y-m-d H:i:s'))
            ->get();

        /** @var Order $order */
        foreach ($orders as $order) {
            $order->seats = 0;
            $order->save();
        }
    }
}
