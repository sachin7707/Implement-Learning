<?php

namespace App\Mail;

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

    /**
     * OrderBooker constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return OrderBooker
     */
    public function build()
    {
        return $this->view('emails.orders.booker')
            ->text('emails.orders.booker_plain');
    }
}
