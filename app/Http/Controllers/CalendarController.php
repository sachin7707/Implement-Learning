<?php

namespace App\Http\Controllers;

use App\Participant;

/**
 * @author jimmiw
 * @since 2019-05-14
 */
class CalendarController extends Controller
{
    public function show($orderHash, $participantHash)
    {
        $order = Order::where('md5(id)', $orderHash)
            ->first();


    }
}
