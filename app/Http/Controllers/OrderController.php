<?php

namespace App\Http\Controllers;

use App\Course;
use App\Maconomy\Service\CourseService;
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
    private $courseService;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService, CourseService $courseService)
    {
        $this->orderService = $orderService;
        $this->courseService = $courseService;
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

        // fetches the course
        $course = Course::findOrFail((int)$request->input('course_id'));

        $order = new Order();
        $order->course_id = $course->id;
        // saving order, before sending it to the order service
//        $order->saveOrFail();

        $requiredSeats = (int)$request->input('seats', 1);

        // reserving the seats on the order
        if ($this->orderService->reserveSeats($order, $requiredSeats)) {
            $order->saveOrFail();
            return response()->json($order);
        }

        return response()->json([
            'error' => 'Not enough seats available',
            'seats_required' => $requiredSeats,
            'seats_available' => $this->courseService->getSeatsAvailable($course),
        ]);
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
