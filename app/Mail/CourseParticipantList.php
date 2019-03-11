<?php

namespace App\Mail;

use App\Course;
use App\MailText;
use App\Order;
use App\Trainer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * This Mailable sends out a participant list to the trainers on a given course.
 * @author jimmiw
 */
class CourseParticipantList extends Mailable
{
    use Queueable, SerializesModels;

    public $course;
    public $trainer;
    public $daysTo;
    /** @var string  */
    public $language;
    public $participants;

    // mail texts
    public $footer;

    /**
     * CourseParticipantList constructor.
     * @param Course $course the course to send information about
     * @param Trainer $trainer the trainer to send the information to
     */
    public function __construct(Course $course, Trainer $trainer, int $daysTo)
    {
        $this->course = $course;
        $this->trainer = $trainer;
        $this->daysTo = $daysTo;
        // language on the email, comes from the trainer
        $this->language = $trainer->language;

        // sets the list of participants
        $this->participants = $this->fetchParticipants($course);

        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, $this->language);
    }

    /**
     * @return CourseParticipantList
     */
    public function build()
    {
        $subject = $this->language === 'da' ? 'Deltagerliste for %Kursusnavn%' : 'Participant list for %Kursusnavn%';

        return $this->view('emails.courses.participantlist')
            ->text('emails.courses.participantlist_plain')
            ->subject(str_replace('%Kursusnavn%', $this->course->getTitle($this->language), $subject));
    }

    /**
     * Fetches the list of participants, that are signed up for the given course
     * @param Course $course
     * @return array the list of participants on the course
     */
    private function fetchParticipants(Course $course): array
    {
        $participantsOnCourse = [];

        // we are only fetching orders, that is not on waiting list
        $orders = Order::where('on_waitinglist', 0)
            // only fetching confirmed (synced to maconomy) orders
            ->where('state', Order::STATE_CONFIRMED)
            ->whereHas('courses', function ($query) use ($course) {
                $query->where('courses.id', $course->id)
                    ->withTrashed();
            })
            ->with(['company'])
            ->get();

        // runs through the orders found, getting the participants
        foreach ($orders as $order) {
            foreach ($order->company->participants as $participant) {
                $participantsOnCourse[] = $participant;
            }
        }

        return $participantsOnCourse;
    }
}
