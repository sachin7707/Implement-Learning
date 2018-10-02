<?php

namespace App\Http\Controllers;

use App\Maconomy\Client\Maconomy;
use App\Order;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class OrderController extends Controller
{
    /** @var Maconomy */
    private $client;

    /**
     * OrderController constructor.
     * @param Maconomy $client
     */
    public function __construct(Maconomy $client)
    {
        $this->client = $client;
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
        // TODO: should we use the $validatedData instead?
        $validatedData = $this->validate($request, [
            'course_id' => 'required'
        ]);

        $order = new Order();

        $order->course_id = $request->input('course_id');
        $order->number_of_participants = $request->input('number_of_participants', 1);
        $order->saveOrFail();

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
        /** @var Order $order */
        $order = Order::findOrFail($id);
        // fetches the course info
        $course = $order->course;

        // fetches the number of seats to reserve
        $requiredSeats = (int)$request->input('seats');

        // calls maconomy to get the current seats available
        $availableSeats = $this->client->getAvailableSeats($course->maconomy_id);

        if ($availableSeats >= $requiredSeats) {
            // updates the number of seats taken
            $order->reserveSeats($requiredSeats, $availableSeats);
        }

        $order->saveOrFail();
    }
}
