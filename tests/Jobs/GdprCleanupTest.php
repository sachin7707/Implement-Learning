<?php

/**
 * @author jimmiw
 * @since 2019-05-23
 */
class GdprCleanupTest extends TestCase
{
    public function testCleanup()
    {
        $job = new \App\Jobs\GdprCleanup();

        $date = $job->getFromDate();

        $orders = $job->getOrders();

        $this->assertNotEmpty($orders);

        foreach ($orders as $order) {
            error_log('order: ' . $order->id);
            foreach ($order->courses as $course) {
                error_log('course: ' . $course->id . ': ' . $course->end_time);
                $this->assertLessThan(
                    $date,
                    $course->end_time,
                    'Order: ' . $order->id .', with course : ' . $course->id . ' has error on end_time'
                );
            }
        }
    }
}