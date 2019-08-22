<?php

namespace App\Mail;

use App\MailText;
use App\Order;
use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * @author jimmiw
 * @since 2018-10-30
 */
class OrderParticipant extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $courses;
    public $participant;
    public $calendarUrl;
    /** @var string the language the email should shown in */
    public $language;

    // mail texts
    public $intro;
    public $footer;
    public $beforeCourseHeader;

    /**
     * OrderParticipant constructor.
     * @param Order $order
     * @param Participant $participant
     */
    public function __construct(Order $order, Participant $participant)
    {
        $this->order = $order;
        $this->language = (string)$order->language ?? 'da';
        $this->courses = $order->getCoursesSorted()->toArray();
        // we wrap the participant in an adapter, to be able to use the data in the email
        $this->participant = $participant;

        // creating the calendar url for the participants
        // TODO: change this url in the future? since it goes directly to the api instead of WP site.
        $this->calendarUrl = '/api/v1/order/'. str_pad($order->id, 8, '0', STR_PAD_LEFT) .'/cal';

        // TODO: should change language! just using DA atm
        // setting general mail texts
        $introType = MailText::TYPE_DEFAULT_PARTICIPANT;
        if ($this->order->on_waitinglist) {
            $introType = MailText::TYPE_WAITINGLIST_PARTICIPANT;
        }

        $this->intro = MailText::getByTypeAndLanguage($introType, $this->language);
        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, $this->language);

        $this->beforeCourseHeader = MailText::getByTypeAndLanguage(
            MailText::TYPE_DEFAULT_PARTICIPANT_BEFORE_COURSE,
            $this->language
        );

        // handles empty before course headers, by using "old" defaults
        if (empty($this->beforeCourseHeader)) {
            $this->beforeCourseHeader = ($this->language === 'da' ? 'F&#248;r kurset skal du' : 'Before course start');
        }
    }

    /**
     * @return OrderParticipant
     */
    public function build()
    {
        // TODO: we need to get the course material to "include" in the email. Kontainer vs Attachment?

        $subject = $this->language === 'da' ? 'Tilmelding til %Kursusnavn%' : 'Signup for %Kursusnavn%';

        return $this->view('emails.orders.participant')
            ->text('emails.orders.participant_plain')
            ->subject(str_replace('%Kursusnavn%', Helper::getTitle($this->order), $subject));
    }
}
