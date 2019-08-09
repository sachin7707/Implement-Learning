<?php

namespace App\Mail;

use App\Course;
use App\Mail\Adapters\Participant;
use App\Trainer;
/**
 * This Mailable sends out a participant list to the trainers on a given course.
 * @author jimmiw
 */
class CourseParticipantList extends MailDefault
{
    public $course;
    public $trainer;
    public $daysTo;
    /** @var array $participants the participants that are signed up */
    public $participants;
    /** @var array $participantsOnWaitingList the participants that are on the waiting list */
    public $participantsOnWaitingList;

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
        // language on the email, comes from the course. It was from trainer, but they changed it in comments on ILI-230
        $this->setLanguage($course->getShortLanguage());

        // sets the participants to show. The participants are sorted into two categories: signed up OR waiting list
        $this->setParticipants($participants);

        $this->initDefaultTexts();
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

    /**
     * @param array $participants
     */
    private function setParticipants(array $participants): void
    {
        // handles participants, that are signed up
        $this->participants = [];
        // participants that are on the waiting list
        $this->participantsOnWaitingList = [];

        /** @var \App\Maconomy\Client\Models\Course\Participant $participant */
        foreach ($participants as $participant) {
            if ($participant->isOnWaitingList()) {
                $this->participantsOnWaitingList[] = $participant;
            } else {
                $this->participants[] = $participant;
            }
        }
    }
}
