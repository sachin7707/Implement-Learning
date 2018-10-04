<?php

namespace App\Jobs;

use App\Course;
use App\Events\CoursesSyncedEvent;
use App\Maconomy\Client\Maconomy;
use App\Maconomy\Collection\CourseCollection;
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
        $courses = $this->getCourses($client);

        foreach ($courses as $course) {
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
                ]
            );
        }

        error_log('before event being dispatched');
        // sends a notification to wordpress
        Event::dispatch(new CoursesSyncedEvent($this->maconomyId));
    }

    /**
     * Fetches the courses from maconomy
     * @param Maconomy $client
     * @return CourseCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getCourses(Maconomy $client)
    {
        // fetches a single course, if a course id was given
        if (! empty($this->maconomyId)) {
            return $client->getCourse($this->maconomyId);
        }

        return $client->getCourses();
    }
}