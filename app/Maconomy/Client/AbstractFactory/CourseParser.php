<?php

namespace App\Maconomy\Client\AbstractFactory;

use App\Maconomy\Collection\CourseCollection;
use App\Maconomy\Model\Course;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseParser implements Parser
{
    /**
     * Parses the course in the given response, returning a nice model instead
     * @param mixed $data the data from the webservice
     * @return Course
     */
    public function parse($data): Course
    {
        $startDate = $data->startingDateField ?? null;
        $endDate = $data->endingDateField ?? null;

        // no start and end dates? must be the newest api (v2.x)
        if ($startDate === null) {
            if (! empty($data->dates)) {
                // use v2.0 parsing, which only have the start date and last end date (just like v1 api)
                if (count($data->dates) === 1) {
                    $startDate = $data->dates[0]->startingDate;
                    $endDate = $data->dates[0]->endingDate;
                }
            }
        }

        $course = new Course();
        $course->maconomyId = $data->courseNumberField ?? $data->courseNumber;
        $course->name = $data->courseNameField ?? $data->courseName;
        $course->price = $data->priceField ?? $data->price;
        $course->maxParticipants = $data->maxParticipantsField ?? $data->maxParticipants;
        $course->minParticipants = $data->minParticipantsField ?? $data->minParticipants;
        $course->startTime = new \DateTime($startDate, new \DateTimeZone('GMT'));
        $course->endTime = new \DateTime($endDate, new \DateTimeZone('GMT'));
        $course->language = $data->weblanguageField ?? $data->weblanguage;
        $course->venueId = $data->venueField ?? $data->venue;
        $course->venueName = $data->venuenameField ?? $data->venuename;
        $course->seatsAvailable = $data->freeSeatsField ?? $data->freeSeats;
        $course->currentParticipants = $data->enrolledField ?? $data->enrolled;

        return $course;
    }

    /**
     * Parses the given webservice data, containing a list of courses and returns a nice CourseCollection
     * @param array $data the list of course from the webservice
     * @return CourseCollection
     */
    public function parseCollection(array $data): CourseCollection
    {
        $courseTypes = [];

        // parsing the courses
        foreach ($data as $courseTypeData) {
            $courseTypes[] = $this->parse($courseTypeData);
        }

        return new CourseCollection($courseTypes);
    }
}