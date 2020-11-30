<?php

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class MaconomyTest extends TestCase
{
    public function testInit()
    {
        $client = $this->getClient();
        static::assertInstanceOf(\App\Maconomy\Client\Maconomy::class, $client);
    }

    /**
     * Fetches all the courses
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetCourses()
    {
        $client = $this->getClient();
        $courses = $client->getCourses();

        static::assertNotEmpty($courses);
    }

    /**
     * Fetches a single course
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetCourse()
    {
        $courseId = '15386-05';

        $client = $this->getClient();
        $courses = $client->getCourse($courseId);
        static::assertNotEmpty($courses);
        static::assertEquals(1, $courses->count());
        // fetches the first course
        $course = $courses->current();
        static::assertNotNull($course);
        static::assertInstanceOf(\App\Maconomy\Model\Course::class, $course);

        static::assertEquals($courseId, $course->maconomyId);
        static::assertEquals("PL1 183 15-17 nov 2010 Få styr på projektopgaven", $course->name);
        static::assertEquals(16500, $course->price);
        static::assertEquals(24, $course->maxParticipants);
        static::assertEquals(0, $course->minParticipants);
        static::assertEquals('01', $course->venueId);
        static::assertEquals('', $course->venueName);
    }

    /**
     * @return \App\Maconomy\Client\Maconomy
     */
    private function getClient()
    {
        return new \App\Maconomy\Client\Maconomy(env('MACONOMY_URL'));
    }
}
