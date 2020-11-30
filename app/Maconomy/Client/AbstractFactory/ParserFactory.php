<?php

namespace App\Maconomy\Client\AbstractFactory;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class ParserFactory
{
    /**
     * @return CourseTypeParser
     */
    public function createCourseTypeParser(): CourseTypeParser
    {
        return new CourseTypeParser();
    }

    /**
     * @return CourseParser
     */
    public function createCourseParser(): CourseParser
    {
        return new CourseParser();
    }
}
