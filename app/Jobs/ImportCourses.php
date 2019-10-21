<?php

namespace App\Jobs;

use App\Course;
use App\CourseDate;
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
     * @throws \Exception
     */
    private function syncCourses(Maconomy $client): void
    {
        $courses = $this->getCourses($client);

        $courseStartShouldBeAfter = new \DateTime();
        // NOTE: we have a "hard cap" of 1 month for courses. This means that courses with a starttime older than
        // 1 month ago should not be saved in the system
        $courseStartShouldBeAfter->sub(new \DateInterval('P1M'));

        // creating a "now" timestamp, to set when we are syncing a course
        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:s');

        foreach ($courses as $course) {
            $courseType = $this->getCourseType($course->maconomyId);

            // checks if the course starttime is more than 1 month old, if yes, skip the sync of the course.
            if ($courseStartShouldBeAfter > $course->startTime) {
                continue;
            }

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
                'deadline' => $signupDeadline,
                'last_sync_date' => $now,
            ];

            /** @var Course $dbCourse */
            $dbCourse = Course::where('maconomy_id', $course->maconomyId)
                ->withTrashed()
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

            // adding the course's module dates to the course in the database
            $this->addCourseModuleDates($dbCourse->id, $course);
        }

        // only delete courses, if we are not syncing a single course, since all other courses would be deleted :)
        if (empty($this->maconomyId)) {
            // deleting "old" courses
            Course::where('last_sync_date', '<', $now)
                ->orWhereNull('last_sync_date')
                ->delete();
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

    /**
     * Adding the given course's module dates (what it's called in maconomy) to the course in the database.
     * @param int $id the curreny db course id
     * @param \App\Maconomy\Model\Course $course the data from maconomy
     */
    private function addCourseModuleDates(int $id, \App\Maconomy\Model\Course $course)
    {
        // deletes the current course dates on the course (if any)
        CourseDate::where('course_id', $id)->delete();

        foreach ($course->dates as $period) {
            $courseDate = new CourseDate([
                'start' => $period->start,
                'end' => $period->end,
                'course_id' => $id,
            ]);
            $courseDate->save();
        }
    }
}
