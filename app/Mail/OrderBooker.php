<?php

namespace App\Mail;

use App\Mail\Adapters\Participant;
use App\Mail\Adapters\ParticipantAdapter;
use App\Order;

/**
 * Mailable builder for the booker of the order.
 * NOTE: "mail til bestiller"
 *
 * @author jimmiw
 * @since 2018-10-30
 */
class OrderBooker extends MailDefault
{
    /** @var Order $order the order to get access to in the views */
    public $order;
    public $courses;

    /** @var Participant[] */
    public $participants;

    /**
     * OrderBooker constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->courses = $order->courses;
        $this->setLanguage((string)$order->language ?? 'da');

        // Converting the participants on the order, to the proper mail participants, so we are sure that
        // they will have correct methods for usage in the email.
        $participants = [];
        foreach ($order->company->participants as $participant) {
            $participants[] = new ParticipantAdapter($participant);
        }
        $this->participants = $participants;

        $this->initDefaultTexts();
    }

    /**
     * @return OrderBooker
     */
    public function build()
    {
        $subject = $this->language === 'da' ? 'Ordrebekræftelse - %Kursusnavn%' : 'Order confirmation - %Kursusnavn%';

        return $this->view('emails.orders.booker')
            ->text('emails.orders.booker_plain')
            ->subject(str_replace('%Kursusnavn%', Helper::getTitle($this->order), $subject));
    }
}
