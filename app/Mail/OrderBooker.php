<?php

namespace App\Mail;

use App\MailText;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable builder for the booker of the order.
 * NOTE: "mail til bestiller"
 *
 * @author jimmiw
 * @since 2018-10-30
 */
class OrderBooker extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Order $order the order to get access to in the views */
    public $order;
    public $courses;

    // mail texts
    public $footer;

    /**
     * OrderBooker constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->courses = $order->courses;

        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, 'da');
    }

    /**
     * @return OrderBooker
     */
    public function build()
    {
        return $this->view('emails.orders.booker')
            ->text('emails.orders.booker_plain')
            ->subject(str_replace('%Kursusnavn%', Helper::getTitle($this->order), 'Ordrebekræftelse - %Kursusnavn%'));
    }
}
