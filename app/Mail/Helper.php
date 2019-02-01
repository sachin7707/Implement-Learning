<?php

namespace App\Mail;

use App\Order;

/**
 * @author jimmiw
 * @since 2019-02-01
 */
class Helper
{
    /**
     * Fetches the email title to use
     * @param Order $order
     * @return string
     */
    public static function getTitle($order)
    {
        $courseName = '';
        if ($order->education) {
            return $order->education->name;
        }

        // not part of an education, just use the first course on the order.
        $course = $order->courses()->first();

        if ($course) {
            $courseName = $course->getTitle();
        }

        return $courseName;
    }
}