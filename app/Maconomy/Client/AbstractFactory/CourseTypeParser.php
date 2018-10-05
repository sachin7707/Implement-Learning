<?php

namespace App\Maconomy\Client\AbstractFactory;

use App\Maconomy\Collection\CourseTypeCollection;
use App\Maconomy\Model\CourseType;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseTypeParser implements Parser
{
    /**
     * Parses the course types in the given response, returning a nice model instead
     * @param mixed $data the data from the webservice
     * @return CourseType
     */
    public function parse($data): CourseType
    {
        $courseType = new CourseType();
        $courseType->number = $data->courseNumber;
        $courseType->name = $data->courseName;
        $courseType->price = $data->price;
        $courseType->maxParticipants = $data->maxParticipantsField;
        $courseType->minParticipants = $data->minParticipantsField;
        $courseType->duration = $data->duration;

        return $courseType;
    }

    /**
     * Parses the given webservice data, containing a list of courses and returns a nice CourseTypeCollection
     * @param array $data the list of coursetypes from the webservice
     * @return CourseTypeCollection
     */
    public function parseCollection(array $data)
    {
        $courseTypes = [];

        // parsing the courses
        foreach ($data as $courseTypeData) {
            $courseTypes[] = $this->parse($courseTypeData);
        }

        return new CourseTypeCollection($courseTypes);
    }
}