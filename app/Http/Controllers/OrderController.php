<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class OrderController extends Controller
{
    /**
     * Shows information about all orders
     */
    public function index()
    {
        return response()->json(Order::orderByDesc('id')->get());
    }

    /**
     * Shows a single orders information
     * @param string $id
     */
    public function show(string $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    /**
     * Creates a new order
     * @param Request $request
     */
    public function create(Request $request)
    {
        $order = new Order();
        $order->course_id = $request->input('course_id');
        $order->number_of_participants = $request->input('number_of_participants', 1);
        $order->save();

        return response()->json($order);
    }
}
