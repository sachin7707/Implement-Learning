<?php

namespace App\Maconomy\Collection;

use App\Maconomy\Model\CourseType;

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
     * @return CourseType
     */
    public function current()
    {
        /** @var CourseType $courseType */
        $courseType = parent::getCurrent();
        return $courseType;
    }
}