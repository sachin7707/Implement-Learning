<?php

namespace App\Http\Controllers;

use App\Order;

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
        
    }

    /**
     * Shows a single orders information
     * @param string $id
     */
    public function show(string $id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order->toJson());
    }
}
