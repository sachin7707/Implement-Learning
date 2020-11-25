<?php

namespace App\Maconomy\Collection;

use App\Maconomy\Model\Course;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class CourseCollection extends Collection
{
    /**
     * OrderCollection constructor.
     * @param array $courses the list of courses
     */
    public function __construct(array $courses)
    {
        $this->dataList = $courses;
    }

    /**
     * @return Course
     */
    public function current()
    {
        /** @var Course $course */
        $course = parent::getCurrent();
        return $course;
    }
}