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
        $orders = $job->getOrders();
    }
}