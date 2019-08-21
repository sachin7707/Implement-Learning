<?php

namespace App\Calendar;

use App\Order;
use Carbon\Carbon;
use Sabre\VObject\Component\VCalendar;

/**
 * Small class for generating a nice calendar object, with course events, using a given order object.
 * @author jimmiw
 * @since 2019-05-22
 */
class OrderCalendar
{
    /** @var string $baseUrl used for prepending to the calendars url */
    private $baseUrl;
    /** @var Order */
    private $order;

    /**
     * OrderCalendar constructor.
     * @param Order $order the order to create the calendar information from
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        // setting the default base url, using the SERVER_URL environment variable
        $this->baseUrl = env('SERVER_ICS_URL');
    }

    /**
     * Sets the base url, to prepend to the link for the calendar
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Fetches the link to the calendar, using the order to generate the url
     * @return string
     */
    public function getLink()
    {
        return $this->baseUrl . '/api/v1/calendar/' . $this->order->getHash();
    }

    /**
     * Fetches the calendar object, with events from the given order.
     * NOTE: we do not use the participant information on the order for anything, just the courses and their info
     * @return VCalendar
     */
    public function getCalendar(): VCalendar
    {
        // initializes the calender
        $calendar = new VCalendar();

        foreach ($this->order->courses as $course) {
            // fetches the course type, so we can get the event dates
            $courseType = $course->coursetype();
            $title = $course->getTitle($course->getShortLanguage());

            // TODO: use $order->isOnWaitingList(), so we can display something in the title

            // fetching the dates
            $dates = $course->getCourseDates();
            // getting times
            $times = $course->getCourseTimes();

            /** @var Carbon $date */
            /** @var int $index the dates index, which we use, when finding it's time (if present) */
            foreach ($dates as $index => $date) {
                $startDate = new Carbon($date);
                $endDate = new Carbon($date);

                // if the times have been added, we add this to the date as well
                if (! empty($times[$index])) {
                    list($startTime,$endTime) = explode('-',$times[$index]);

                    $startDate->setTimeFromTimeString($startTime);
                    $endDate->setTimeFromTimeString($endTime);
                }

                $calendarData = [
                    'SUMMARY' => $title,
                    'DTSTART' => $startDate,
                    'DTEND' => $endDate,
                ];

                // if the course has a location associated, we add the address for that as well
                if ($course->location) {
                    $calendarData['LOCATION'] = $course->location->getDisplayString();
                }

                // creating the calendar event, using the start and end dates
                $calendar->add('VEVENT', $calendarData);
            }
        }

        return $calendar;
    }
}
