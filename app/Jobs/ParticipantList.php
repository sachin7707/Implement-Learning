<?php

namespace App\Jobs;

use App\Course;
use App\Mail\CourseParticipantList;
use App\Mail\Helper;

/**
 * @author jimmiw
 * @since 2019-02-05
 */
class ParticipantList extends Job
{
    /** @var Course $course the course to send out participant lists for (on mail) */
    private $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function handle()
    {
        foreach ($this->course->trainers as $trainer) {
            Helper::getMailer($trainer->email, false)
                ->queue(new CourseParticipantList($this->course, $trainer));
        }
    }
}
