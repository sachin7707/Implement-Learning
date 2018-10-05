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
        $course = new Course();
        $course->maconomyId = $data->courseNumberField;
        $course->name = $data->courseNameField;
        $course->price = $data->priceField;
        $course->maxParticipants = $data->maxParticipantsField;
        $course->minParticipants = $data->minParticipantsField;
        $course->startTime = new \DateTime($data->startingDateField, new \DateTimeZone('GMT'));
        $course->endTime = new \DateTime($data->endingDateField, new \DateTimeZone('GMT'));
        $course->language = $data->weblanguageField;
        $course->venueId = $data->venueField;
        $course->venueName = $data->venuenameField;
        $course->seatsAvailable = $data->freeSeatsField;
        $course->currentParticipants = $data->enrolledField;

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