<?php

namespace App\Jobs;

use App\Course;
use App\Events\CoursesSyncedEvent;
use App\Maconomy\Client\Maconomy;
use App\Maconomy\Collection\CourseCollection;
use App\CourseType;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Event;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class ImportCourses extends Job
{
    /** @var string */
    private $maconomyId;

    /**
     * ImportCourses constructor.
     * @param string $maconomyId the courses maconomy id to sync, or empty for all courses
     */
    public function __construct(string $maconomyId = '')
    {
        $this->maconomyId = $maconomyId;
    }

    /**
     * Starts the import of the courses
     * @param Maconomy $client the maconomy client to use, when syncing the course(s)
     * @throws GuzzleException
     */
    public function handle(Maconomy $client)
    {
        // syncs the course types first
        $this->syncCourseTypes($client);
        // then syncs the courses
        $this->syncCourses($client);

        // sends a notification to wordpress
        Event::dispatch(new CoursesSyncedEvent($this->maconomyId));
    }

    /**
     * Fetches the course type, using the given course's maconomy id.
     * @param string $maconomyId
     * @return CourseType
     */
    private function getCourseType(string $maconomyId)
    {
        preg_match('/^[0-9]+/', $maconomyId, $matches);

        $courseType = CourseType::where('number', $matches[0])
            ->first();
        
        return $courseType;
    }

    /**
     * Fetches the courses from maconomy
     * @param Maconomy $client
     * @return CourseCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getCourses(Maconomy $client): CourseCollection
    {
        // fetches a single course, if a course id was given
        if ($this->isSingleCourseSync()) {
            return $client->getCourse($this->maconomyId);
        }

        return $client->getCourses();
    }

    /**
     * Syncs the course types
     * @param Maconomy $client
     * @throws GuzzleException
     */
    private function syncCourseTypes(Maconomy $client): void
    {
        // Course types should NOT be synced, if we are just syncing a single course
        if ($this->isSingleCourseSync()) {
            return;
        }

        $courseTypes = $client->getCourseTypes();

        foreach ($courseTypes as $courseType) {
            // updates the course type in the database
            CourseType::updateOrCreate(
                [
                    'number' => $courseType->number
                ],
                [
                    'name' => $courseType->name,
                    'participants_max' => $courseType->maxParticipants,
                    'participants_min' => $courseType->minParticipants,
                    'duration' => $courseType->duration,
                    'price' => $courseType->price,
                ]
            );
        }
    }

    /**
     * Syncs the courses
     * @param Maconomy $client
     * @throws GuzzleException
     */
    private function syncCourses(Maconomy $client): void
    {
        $courses = $this->getCourses($client);

        foreach ($courses as $course) {
            $courseType = $this->getCourseType($course->maconomyId);

            // updates the courses in the database
            Course::updateOrCreate(
                [
                    'maconomy_id' => $course->maconomyId,
                ],
                [
                    'name' => $course->name,
                    'language' => $course->language,
                    'venue_number' => $course->venueId,
                    'venue_name' => $course->venueName,
                    'start_time' => $course->startTime,
                    'end_time' => $course->endTime,
                    'participants_max' => $course->maxParticipants,
                    'participants_min' => $course->minParticipants,
                    'participants_current' => $course->currentParticipants,
                    'seats_available' => $course->seatsAvailable,
                    'price' => $course->price,
                    'coursetypeId' => $courseType->id ?? null
                ]
            );
        }
    }

    /**
     * Checks if we are just syncing a single course.
     * @return bool
     */
    private function isSingleCourseSync(): bool
    {
        return !empty($this->maconomyId);
    }
}