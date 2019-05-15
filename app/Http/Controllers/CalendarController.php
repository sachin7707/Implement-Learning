<?php

namespace App\Http\Controllers;

use App\Course;
use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sabre\VObject\Component\VCalendar;

/**
 * @author jimmiw
 * @since 2019-05-14
 */
class CalendarController extends Controller
{
    public function show($orderHash)
    {
        $order = Order::where(DB::raw('md5(id)'), $orderHash)
            ->first();

        // initializes the calender
        $calendar = new VCalendar();

        foreach ($order->courses as $course) {
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

                // creating the calendar event, using the start and end dates
                $calendar->add('VEVENT', [
                    'SUMMARY' => $title,
                    'DTSTART' => $startDate,
                    'DTEND' => $endDate,
                ]);
            }
        }

        // returns the calendar data
        echo $calendar->serialize();
    }
}
