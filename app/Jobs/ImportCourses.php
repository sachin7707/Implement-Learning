<?php

namespace App\Jobs;

use App\Course;
use App\Events\CoursesSyncedEvent;
use App\Maconomy\Client\Maconomy;
use App\Maconomy\Collection\CourseCollection;
use App\CourseType;
use App\Maconomy\Service\CourseService;
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
     * @param CourseService $courseService
     * @throws GuzzleException
     */
    public function handle(Maconomy $client, CourseService $courseService)
    {
        // syncs the course types first
        $this->syncCourseTypes($client);
        // then syncs the courses
        $this->syncCourses($client, $courseService);

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
    private function syncCourses(Maconomy $client, CourseService $courseService): void
    {
        $courses = $this->getCourses($client);

        foreach ($courses as $course) {
            $courseType = $this->getCourseType($course->maconomyId);

            // creating a deadline for course signup
            $signupDeadline = new \DateTime($course->startTime->format('r'));
            // default signup deadline is two weeks before the start date
            $signupDeadline->sub(new \DateInterval('P14D'));


            $data = [
                'name' => $course->name,
                'language' => $course->language,
                'venue_number' => $course->venueId,
                'venue_name' => $course->venueName,
                'start_time' => $course->startTime,
                'end_time' => $course->endTime,
                'participants_min' => $course->minParticipants,
                'participants_current' => $course->currentParticipants,
                'price' => $course->price,
                'seats_available' => $course->seatsAvailable,
                'coursetype_id' => $courseType->id ?? null,
                'deadline' => $signupDeadline
            ];

            /** @var Course $dbCourse */
            $dbCourse = Course::where('maconomy_id', $course->maconomyId)
                ->first();

            // update existing course
            if ($dbCourse) {
                // handling signup date on course update
                if ($dbCourse->deadline !== null) {
                    // removing the deadline date, to be sure we don't overwrite stuff from WP
                    unset($data['deadline']);
                }

                $dbCourse->update(array_merge($data, [
                    // also calculating the number of seats available, since max participants "can be" changed locally
                    'seats_available' => $dbCourse->participants_max - $course->currentParticipants
                ]));
            } else {
                // create a new course, by adding a bit more details
                $dbCourse = new Course(array_merge($data, [
                    // maconomy id should only be set on create
                    'maconomy_id' => $course->maconomyId,
                    // max participants is only set on create, since it can be changed in the wp admin
                    'participants_max' => $course->maxParticipants,
                ]));

                $dbCourse->save();
            }
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