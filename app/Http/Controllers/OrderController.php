<?php

namespace App\Http\Controllers;

use App\Maconomy\Client\Maconomy;
use App\Maconomy\Service\OrderService;
use App\Order;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class OrderController extends Controller
{
    /** @var OrderService */
    private $orderService;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        // validating that we have a course_id set
        $this->validate($request, [
            'course_id' => 'required'
        ]);

        $order = new Order();
        $order->course_id = $request->input('course_id');
        // saving order, before sending it to the order service
        $order->saveOrFail();

        // reserving the seats on the order
        $this->orderService->reserveSeats($order, (int)$request->input('number_of_participants', 1));

        return response()->json($order);
    }

    /**
     * Updates an order, adding more information about participants etc to it.
     * @param Request $request
     * @param string $id the order id
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        // validating that we have seats set
        // TODO: should we use the $validatedData instead?
        $this->validate($request, [
            'seats' => 'required'
        ]);

        /** @var Order $order */
        $order = Order::findOrFail($id);

        // seats are required, so do NOT use a default value
        $this->orderService->reserveSeats($order, (int)$request->input('seats'));
    }
}
