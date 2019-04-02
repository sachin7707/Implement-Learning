<?php

namespace App\Jobs;

use App\Course;
use App\Maconomy\Client\Maconomy;
use App\Mail\CourseParticipantList;
use App\Mail\Helper;
use Carbon\Carbon;

/**
 * @author jimmiw
 * @since 2019-02-05
 */
class SendParticipantList extends Job
{
    const TYPE_ONE_DAY_BEFORE = 1;
    const TYPE_FIVE_DAYS_BEFORE = 5;

    private $courseId = 0;
    private $type = 0;

    /** @var Maconomy */
    private $client;

    /**
     * Sets the course to send emails for
     * @param int $courseId
     */
    public function setCourse(int $courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     * Sets the email type to send
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Sends the participant lists
     * @param Maconomy $client the maconomy client is injected into the job, so we can get the list of participants
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Maconomy $client)
    {
        $this->client = $client;

        // handle sending to a single course
        if ($this->courseId > 0) {
            $course = Course::where('id', $this->courseId)
                ->withTrashed()
                ->first();

            // if the course was found, send the participant list to the trainers
            if ($course) {
                $this->sendParticipantListToTrainers(
                    $course,
                    $this->type
                );
            }

            return;
        }

        // sends 5 days before reminders
        $this->send5DaysReminder();

        // sends 1 day before reminders
        $this->send1DayReminder();
    }

    /**
     * Sends the list of participants, to the trainers on the given course
     * @param Course $course the course to use, when sending out a participant list
     * @param int $daysTo number of days until the course starts
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendParticipantListToTrainers(Course $course, int $daysTo)
    {
        $participants = $this->client->getParticipantsOnCourse($course->maconomy_id);

        // NOTE: type is not used atm, it "should" be used though.
        foreach ($course->trainers as $trainer) {
            Helper::getMailer($trainer->email, false)
                ->queue(new CourseParticipantList(
                    $course,
                    $trainer,
                    $daysTo,
                    $participants
                ));
        }
    }

    /**
     * Finds the courses to send a "5 day before" reminder to, and sends the trainers' the participant list
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function send5DaysReminder(): void
    {
        $courses = $this->getCoursesForType(self::TYPE_FIVE_DAYS_BEFORE);

        /** @var Course $course */
        foreach ($courses as $course) {
            $this->sendParticipantListToTrainers($course, self::TYPE_FIVE_DAYS_BEFORE);

            // updates the course, to set the reminder as being sent
            $course->reminder5days = 1;
            $course->save();
        }
    }

    /**
     * Finds the courses to send a "1 day before" reminder to, and sends the trainers' the participant list
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function send1DayReminder()
    {
        $courses = $this->getCoursesForType(self::TYPE_ONE_DAY_BEFORE);

        /** @var Course $course */
        foreach ($courses as $course) {
            $this->sendParticipantListToTrainers($course, self::TYPE_ONE_DAY_BEFORE);

            // updates the course, to set the reminder as being sent
            $course->reminder1day = 1;
            $course->save();
        }
    }

    /**
     * Fetches the courses, for the given type
     * @param int $type the type of reminder field to search for
     * @return array the list of courses to send to
     * @throws \Exception
     */
    private function getCoursesForType(int $type)
    {
        $now = new Carbon();
        $now->add(new \DateInterval('P' . $type . 'D'));

        $query = Course::whereDate('start_time', 'like', $now->format('Y-m-d') . '%');

        if ($type === self::TYPE_FIVE_DAYS_BEFORE) {
            $query->where('reminder5days', 0);
        } else {
            $query->where('reminder1day', 0);
        }

        return $query->get();
    }
}
