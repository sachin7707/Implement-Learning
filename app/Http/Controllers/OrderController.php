<?php

namespace App\Http\Controllers;

use App\Course;
use App\Jobs\ImportCourses;
use App\Maconomy\Service\CourseService;
use App\Maconomy\Service\OrderService;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

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
            'maconomy_id' => 'required'
        ]);

        // fetches the course
        $course = Course::where('maconomy_id', $request->input('maconomy_id'))->first();

        $order = new Order();
        $order->state = Order::STATE_NEW;
        $order->course_id = $course->id;
        // saving order, before sending it to the order service
//        $order->saveOrFail();

        $requiredSeats = (int)$request->input('seats', 1);

        // reserving the seats on the order
        if ($this->orderService->reserveSeats($order, $requiredSeats)) {
            $order->saveOrFail();

            // Sends an event to update the course, if needed
            $this->updateCourse($order->course, $order);

            return response()->json($order);
        }

        return response()->json($this->getNotEnoughSeatsError($requiredSeats, $course));
    }

    /**
     * Updates an order, adding more information about participants etc to it.
     * @param Request $request
     * @param string $id the order id
     * @return \Illuminate\Http\JsonResponse
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

        $requiredSeats = (int)$request->input('seats', 1);

        // seats are required, so do NOT use a default value
        if ($this->orderService->reserveSeats($order, $requiredSeats)) {
            // Sends an event to update the course, if needed
            $this->updateCourse($order->course, $order);

            return response()->json($order);
        }

        return response()->json($this->getNotEnoughSeatsError($requiredSeats, $order->course, $order));
    }

    /**
     * Fetches a nice error message, saying that there are not enough seats left to reserve.
     * @param int $requiredSeats the number of required seats
     * @param Course $course the course to get the current number of seats from
     * @param Order|null $order the current order (if any)
     * @return array
     */
    private function getNotEnoughSeatsError(int $requiredSeats, Course $course, Order $order = null): array
    {
        // Sends an event to update the course, if needed
        $this->updateCourse($course, $order);

        return [
            'error' => 'Not enough seats available',
            'seats_required' => $requiredSeats,
            'seats_available' => $course->getAvailableSeats($order),
        ];
    }

    /**
     * @param Course $course
     * @param Order|null $order
     */
    private function updateCourse(Course $course, Order $order = null): void
    {
        // no seats available? tell wordpress to resync the course, to bust the cache in their end
        if ($course->getAvailableSeats($order) <= 0) {
            Queue::later(1, new ImportCourses($course->maconomy_id));
        }
    }
}
