<?php

namespace App\Mail;

use App\Order;
use Illuminate\Support\Facades\Log;
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
            return $order->education->getTitle($order->language);
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
        // if the email is NOT in whitelist
        if (! self::isWhiteListed($receiver)) {
            Mail::fake();
        }

        // queues the mail to the booker
        $mailer = Mail::to($receiver);

        if (! empty(env('MAIL_ORDER_BCC_EMAIL')) && $withBcc === true) {
            // bcc'ing the mail to implement as well
            $mailer->bcc(env('MAIL_ORDER_BCC_EMAIL'));
        }

        return $mailer;
    }

    /**
     * Checks if the given email is in the whitelist
     * @param string $email the email to check
     * @return bool true if the email is whitelisted, else false
     */
    public static function isWhiteListed(string $email): bool
    {
        if (empty(env('WHITELIST'))) {
            Log::info('No whitelist set');
            // no whitelist? just return true :D
            return true;
        }

        // we are exploding the whitelist emails (or domains) to get a nice array
        $whitelist = explode(',', env('WHITELIST'));

        return (in_array($email, $whitelist) || in_array(substr(strrchr($email, '@'), 0), $whitelist));
    }
}
