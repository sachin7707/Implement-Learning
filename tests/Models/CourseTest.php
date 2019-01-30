<?php

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
        $course = App\Course::find(88);
        $this->assertInstanceOf(App\Course::class, $course);
        $this->assertInstanceOf(App\CourseType::class, $course->coursetype);
        echo $course->coursetype->duration;
    }
}
