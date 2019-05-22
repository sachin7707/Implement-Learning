<?php

namespace App\Http\Controllers;

use App\Calendar\OrderCalendar;
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

        // initializes the calendar generator
        $generator = new OrderCalendar($order);
        // constructs the calendar
        $calendar = $generator->getCalendar();

        // returns the calendar data
        echo $calendar->serialize();
    }
}
