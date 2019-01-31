<?php

use App\Order;

/**
 * @author jimmiw
 * @since 2019-01-31
 */
class OrderTest extends TestCase
{
    public function testOrderByPivot()
    {
        $order = Order::find(21);

        $this->assertEquals(2, count($order->courses));
        echo $order->courses()->toSql();
        foreach ($order->courses as $course) {
            echo $course->id;
        }
    }
}
