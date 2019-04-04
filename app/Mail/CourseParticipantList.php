<?php

namespace App\Mail;

use App\Course;
use App\Mail\Adapters\Participant;
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
    /** @var string  */
    public $language;
    public $participants;

    // mail texts
    public $footer;

    /**
     * CourseParticipantList constructor.
     * @param Course $course the course to send information about
     * @param Trainer $trainer the trainer to send the information to
     * @param int $daysTo number of days until the given course starts
     * @param Participant[] $participants the list of participants that are on the course
     */
    public function __construct(Course $course, Trainer $trainer, int $daysTo, array $participants)
    {
        $this->course = $course;
        $this->trainer = $trainer;
        $this->daysTo = $daysTo;
        // language on the email, comes from the trainer
        $this->language = $trainer->language;

        $this->participants = $participants;

        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, $this->language);
    }

    /**
     * @return CourseParticipantList
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function build()
    {
        $subject = $this->language === 'da' ? 'Deltagerliste for %Kursusnavn%' : 'Participant list for %Kursusnavn%';

        return $this->view('emails.courses.participantlist')
            ->text('emails.courses.participantlist_plain')
            ->subject(str_replace('%Kursusnavn%', $this->course->getTitle($this->language), $subject));
    }
}
