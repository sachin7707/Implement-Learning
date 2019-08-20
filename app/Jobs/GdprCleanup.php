<?php

namespace App\Jobs;

use App\Order;

/**
 * Removes orders, where the newest course is over 3 months old.
 * We do not need this information anymore in the system (since all courses are "complete") and it's a
 * GDPR issue.
 * @author jimmiw
 * @since 2019-05-23
 */
class GdprCleanup extends Job
{
    /** @var Order $order if this is set, only one order is being "removed" */
    private $order;

    /**
     * GdprCleanup constructor.
     * @param Order $order
     */
    public function __construct(Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Main entry point in the job, starts the actual cleanup process
     */
    public function handle()
    {
        $orders = $this->getOrders();

        // no orders to delete, just return early
        if (empty($orders)) {
            return;
        }
    }

    /**
     * Fetches the orders that needs to be deleted
     * @return Order[]
     * @throws \Exception
     */
    public function getOrders(): array
    {
        // handles a single order, given when the job was created. This is mostly used for testing :)
        if ($this->order) {
            return [$this->order];
        }

        // fetches the from date, currently active
        $fromDate = $this->getFromDate();

        // fetch orders, where the created date is at fromDate or older
        $allOrders = Order::where('created_at', '<', $fromDate)
            ->with('courses')
            ->get();

        $orders = [];
        foreach ($allOrders as $order) {
            $allCoursesAreDone = true;

            // Checks that the courses on the order are ALL with an end time according to fromDate.
            // This is done to ensure, that all the courses are done, before we delete the order
            foreach ($order->courses as $course) {
                if ($course->end_time > $fromDate) {
                    $allCoursesAreDone = false;
                    break; // stopping the loop
                }
            }

            if ($allCoursesAreDone) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    /**
     * From date is the set max age an order can have.
     * @return \DateTime
     * @throws \Exception
     */
    public function getFromDate(): \DateTime
    {
        $fromDate = new \DateTime();
        // removing 3 months from the current date
        $fromDate->sub(new \DateInterval('P3M'));

        return $fromDate;
    }
}
