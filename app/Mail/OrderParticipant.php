<?php

namespace App\Mail;

use App\Order;
use App\Participant;
use Illuminate\Mail\Mailable;

/**
 * @author jimmiw
 * @since 2018-10-30
 */
class OrderParticipant extends Mailable
{
    public $order;
    public $participant;

    /**
     * OrderParticipant constructor.
     * @param Order $order
     */
    public function __construct(Order $order, Participant $participant)
    {
        $this->order = $order;
        $this->participant = $participant;
    }

    /**
     * @return OrderParticipant
     */
    public function build()
    {
        // TODO: we need to get the course material to "include" in the email. Kontainer vs Attachment?

        // TODO: check if we are sending a waiting list email
        // $this->order->isOnWaitingList();

        return $this->view('emails.orders.participant')
            ->text('emails.orders.participant_plain');
    }
}
