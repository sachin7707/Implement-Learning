<?php

namespace App\Mail;

use App\Order;
use Illuminate\Support\Facades\Mail;

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

    /**
     * Generates a mail object, with BCC email added
     * @param string $receiver
     * @param bool $withBcc true if you want to BCC mail implement, else false
     * @return \Illuminate\Mail\PendingMail
     */
    public static function getMailer(string $receiver, bool $withBcc)
    {
        // queues the mail to the booker
        $mailer = Mail::to($receiver);

        if (! empty(env('MAIL_ORDER_BCC_EMAIL')) && $withBcc === true) {
            // bcc'ing the mail to implement as well
            $mailer->bcc(env('MAIL_ORDER_BCC_EMAIL'));
        }

        return $mailer;
    }
}
