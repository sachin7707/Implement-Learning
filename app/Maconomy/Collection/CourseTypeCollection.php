<?php

namespace App\Maconomy\Collection;

use App\Maconomy\Model\Course;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class CourseTypeCollection extends Collection
{
    /**
     * @param array $courseTypes the list of coursetypes
     */
    public function __construct(array $courseTypes)
    {
        $this->dataList = $courseTypes;
    }

    /**
     * @return Course
     */
    public function current()
    {
        /** @var Course $courseType */
        $courseType = parent::getCurrent();
        return $courseType;
    }
}