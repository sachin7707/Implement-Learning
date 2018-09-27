<?php

namespace App\Jobs;

use App\Course;
use App\Events\CoursesSyncedEvent;
use App\Maconomy\Client\Maconomy;
use App\Maconomy\Collection\CourseCollection;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class ImportCourses extends Job
{
    private $courseId;

    /**
     * ImportCourses constructor.
     * @param int $courseId
     */
    public function __construct(int $courseId = 0)
    {
        $this->courseId = $courseId;
    }

    /**
     * Starts the import of the courses
     */
    public function handle(Maconomy $client)
    {
        $courses = $this->getCourses($client);

        foreach ($courses as $course) {
            // updates the courses in the database
            Course::updateOrCreate(
                [
                    'maconomy_id' => $course->id,
                ],
                [
                    'start_time' => $course->startTime,
                    'end_time' => $course->endTime,
                    'participants_max' => $course->maxParticipants,
                    'participants_current' => $course->currentParticipants,
                    'price' => $course->price,
                ]
            );
        }

        // sends a notification to wordpress
        event(new CoursesSyncedEvent($this->courseId));
    }

    /**
     * Fetches the courses from maconomy
     * @param Maconomy $client
     * @return CourseCollection
     */
    private function getCourses(Maconomy $client)
    {
        // fetches a single course, if a course id was given
        if ($this->courseId !== 0) {
            return $client->getCourse($this->courseId);
        }

        return $client->getCourses();
    }
}