<?php

use App\Course;
use App\CourseType;

/**
 * @author jimmiw
 * @since 2019-01-30
 */
class CourseTest extends TestCase
{
    /**
     * Internal testing :)
     */
    public function te1stAlive()
    {
        $course = Course::find(88);
        $this->assertInstanceOf(Course::class, $course);
        $this->assertInstanceOf(CourseType::class, $course->coursetype);
        echo $course->coursetype->duration;
    }

    public function testPeriods()
    {
        $course = new Course([
            'maconomy_id' => 'tester1',
            'start_time' => new DateTime('2019-05-05 00:00:00'),
            'end_time' => new DateTime('2019-05-10 00:00:00'),
            'deadline' => new DateTime('2019-04-10 00:00:00'),
            'periods' => '20190505-20190507,20190509-20190510',
            'coursetype' => new CourseType([
                'name' => 'test course type',
                'number' => 'some number',
                'price' => 10000,
                'duration' => 5
            ])
        ]);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertInstanceOf(CourseType::class, $course->coursetype);

        print_r($course->getCoursePeriodsFormatted());
        print_r($course->getCourseDates());
        print_r($course->getCourseDatesFormatted());
    }
}
