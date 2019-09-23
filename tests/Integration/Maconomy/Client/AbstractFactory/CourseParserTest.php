<?php

use App\Maconomy\Client\AbstractFactory\CourseParser;

/**
 * @author jimmiw
 * @since 2019-09-23
 */
class CourseParserTest extends TestCase
{
    public function testIsAlive()
    {
        $this->assertTrue(true);
    }

    /**
     * Current (2019-09-23 09:54) live version of the data set
     * @throws Exception
     */
    public function testParseCourseDataV1()
    {
        $data = json_decode('{
            "courseNumberField": "25172-04",
            "courseNameField": "Basis faciliteringsuddannelsen (7. okt. +23.-24. okt. +4. dec. 2019)",
            "durationField": "4 days 8.00 hours",
            "priceField": 22500.0,
            "maxParticipantsField": 24,
            "minParticipantsField": 8,
            "weblanguageField": "da",
            "startingDateField": "2019-10-07T00:00:00",
            "endingDateField": "2019-12-04T00:00:00",
            "enrolledField": 1,
            "freeSeatsField": 23,
            "venueField": " ",
            "venuenameField": "",
            "PropertyChanged": null
        }');

        $startDate = new DateTime('2019-10-07 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $endDate = new DateTime('2019-12-04 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $this->checkCourseData($data, $startDate, $endDate, 'da', 23);
    }

    /**
     * This is sort of a "mid version" that was on dev for a long time.
     * @throws Exception
     */
    public function testParseCourseDataV2()
    {
        $data = json_decode('{
            "courseNumber": "25172-04",
            "courseName": "Basis faciliteringsuddannelsen (7. okt. +23.-24. okt. +4. dec. 2019)",
            "duration": "4 days 8.00 hours",
            "price": 22500.0,
            "maxParticipants": 24,
            "minParticipants": 8,
            "weblanguage": "da",
            "enrolled": 3,
            "freeSeats": 21,
            "venue": " ",
            "venuename": "",
            "active": true,
            "dates": [
                {
                    "startingDate": "2019-10-07T00:00:00",
                    "endingDate": "2019-12-04T00:00:00"
                },
                {
                    "startingDate": "2019-10-07T00:00:00",
                    "endingDate": "2019-12-04T00:00:00"
                },
                {
                    "startingDate": "2019-10-07T00:00:00",
                    "endingDate": "2019-12-04T00:00:00"
                }
            ]
        }');

        // note that start and end is set to null, since the parser actually fails at more than 1 dates[] objects
        $this->checkCourseData($data, null, null, 'da', 21);
    }

    /**
     * Mid version again, but this time with only 1 starting+ending date in dates. This makes it return the
     * proper start and end date.
     * @throws Exception
     */
    public function testParseCourseDataV2b()
    {
        $data = json_decode('{
            "courseNumber": "25172-04",
            "courseName": "Basis faciliteringsuddannelsen (7. okt. +23.-24. okt. +4. dec. 2019)",
            "duration": "4 days 8.00 hours",
            "price": 22500.0,
            "maxParticipants": 24,
            "minParticipants": 8,
            "weblanguage": "da",
            "enrolled": 3,
            "freeSeats": 21,
            "venue": " ",
            "venuename": "",
            "active": true,
            "dates": [
                {
                    "startingDate": "2019-10-07T00:00:00",
                    "endingDate": "2019-12-04T00:00:00"
                }
            ]
        }');

        $startDate = new DateTime('2019-10-07 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $endDate = new DateTime('2019-12-04 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $this->checkCourseData($data, $startDate, $endDate, 'da', 21);
    }

    /**
     * This version uses the dates from ILI-733 properly
     * @throws Exception
     */
    public function testParseCourseDataV3()
    {
        $data = json_decode('{
            "courseNumber": "25172-04",
            "courseName": "Basis faciliteringsuddannelsen (7. okt. +23.-24. okt. +4. dec. 2019)",
            "duration": "4 days 8.00 hours",
            "price": 22500.0,
            "maxParticipants": 24,
            "minParticipants": 8,
            "weblanguage": "da",
            "coursestartingDate": "2019-10-07T00:00:00",
            "courseendingDate": "2019-12-04T00:00:00",
            "enrolled": 3,
            "freeSeats": 21,
            "venue": " ",
            "venuename": "",
            "active": true,
            "dates": [
                {
                    "startingDate": "2019-10-07T08:00:00",
                    "endingDate": "2019-10-07T15:00:00"
                },
                {
                    "startingDate": "2019-10-23T00:00:00",
                    "endingDate": "2019-10-24T00:00:00"
                },
                {
                    "startingDate": "2019-12-04T00:00:00",
                    "endingDate": "2019-12-04T00:00:00"
                }
            ]
        }');

        $startDate = new DateTime('2019-10-07 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $endDate = new DateTime('2019-12-04 00:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE));
        $course = $this->checkCourseData($data, $startDate, $endDate, 'da', 23);

        $this->assertEquals(3, count($course->dates));
        // just testing the first date object in the dates
        $this->assertEquals(
            new DateTime('2019-10-07 08:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE)),
            $course->dates[0]->start
        );
        $this->assertEquals(
            new DateTime('2019-10-07 15:00:00', new DateTimeZone(CourseParser::DEFAULT_TIMEZONE)),
            $course->dates[0]->end
        );
    }

    /**
     * Using a general method to use, when checking the course, so we can pass different data to it, and
     * be sure that we are checking the same things always
     * @param stdClass $data the json data from the api
     * @param DateTime $startDate the starting date (used in checks)
     * @param DateTime $endDate the ending date (used in checks)
     * @param string $language
     * @param int $seats
     * @return \App\Maconomy\Model\Course The parsed course, so we can do further checks if needed.
     * @throws Exception
     */
    private function checkCourseData(stdClass $data, ?DateTime $startDate, ?DateTime $endDate, string $language, int $seats)
    {
        $parser = new CourseParser();
        $course = $parser->parse($data);
        $this->assertNotNull($course);

        // comparing start dates
        $this->assertEquals($startDate, $course->startTime);
        $this->assertEquals($endDate, $course->endTime);

        $this->assertEquals($data->courseNumberField ?? $data->courseNumber, $course->maconomyId);
        $this->assertEquals($language, $course->language);
        $this->assertEquals($seats, $course->seatsAvailable);

        return $course;
    }
}