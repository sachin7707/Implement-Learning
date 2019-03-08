<?php

namespace App\Mail;

use App\Course;
use App\MailText;
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

        // TODO: add language handling (en/da)

        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, 'da');
    }

    /**
     * @return CourseParticipantList
     */
    public function build()
    {
        return $this->view('emails.courses.participantlist')
            ->text('emails.courses.participantlist_plain')
            ->subject(str_replace('%Kursusnavn%', $this->course->getTitle(), 'Deltagerliste for %Kursusnavn%'));
    }

}
