<?php

namespace App\Jobs;

use App\Course;
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

    /**
     * Sends the participant lists
     * @throws \Exception
     */
    public function handle()
    {
        // sends 5 days before reminders
        $this->send5DaysReminder();

        // sends 1 day before reminders
        $this->send1DayReminder();
    }

    /**
     * Sends the list of participants, to the trainers on the given course
     * @param Course $course the course to use, when sending out a participant list
     * @param int $type the type to send. @see TYPE_X
     */
    private function sendParticipantListToTrainers(Course $course, int $type)
    {
        // NOTE: type is not used atm, it "should" be used though.
        foreach ($course->trainers as $trainer) {
            Helper::getMailer($trainer->email, false)
                ->queue(new CourseParticipantList($course, $trainer));
        }
    }

    /**
     * Finds the courses to send a "5 day before" reminder to, and sends the trainers' the participant list
     * @throws \Exception
     */
    private function send5DaysReminder(): void
    {
        $now = new Carbon();
        $now->add(new \DateInterval('P5D'));

        $courses = Course::whereDate('start_time', '=', $now->format('Y-m-d'))
            ->where('reminder5days', 0)
            ->get();

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
     */
    private function send1DayReminder()
    {
        $now = new Carbon();
        $now->add(new \DateInterval('P1D'));

        $courses = Course::whereDate('start_time', '=', $now->format('Y-m-d'))
            ->where('reminder1day', 0)
            ->get();

        /** @var Course $course */
        foreach ($courses as $course) {
            $this->sendParticipantListToTrainers($course, self::TYPE_ONE_DAY_BEFORE);

            // updates the course, to set the reminder as being sent
            $course->reminder1day = 1;
            $course->save();
        }
    }
}
