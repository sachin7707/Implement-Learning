<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Resources\Order as OrderResource;
use App\Jobs\ImportCourses;
use App\Jobs\SyncOrder;
use App\Maconomy\Service\CourseService;
use App\Maconomy\Service\OrderService;
use App\Order;
use Illuminate\Database\Eloquent\Collection;
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
        return response()->json(OrderResource::collection(Order::orderByDesc('id')->get()));
    }

    /**
     * Shows a single orders information
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $order = Order::findOrFail($id);

        return response()->json(new OrderResource($order));
    }

    /**
     * Creates a new order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'education_id' => 'optional|integer'
        ]);

        // creates the new order object, and returns the data
        $order = new Order();
        $order->state = Order::STATE_NEW;

        // adds the education if available
        if ($request->input('education_id')) {
            $order->education_id = $request->input('education_id');
        }

        $order->save();

        return response()->json(new OrderResource($order));
    }

    /**
     * Updates an order, adding more information about participants etc to it.
     * @param Request $request
     * @param string $id the order id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(Request $request, $id)
    {
        // validating that we have seats set
        // TODO: should we use the $validatedData instead?
        $this->validate($request, [
            'seats' => 'required|integer',
            'courses' => 'required|array',
            'education_id' => 'optional|integer'
        ]);

        /** @var Order $order */
        $order = Order::findOrFail($id);

        // adds the education if available
        if ($request->input('education_id')) {
            $order->education_id = $request->input('education_id');
            $order->save();
        }

        // fetches the list of courses to use
        $courseKeys = $request->input('courses');
        $courses = Course::whereIn('maconomy_id', $courseKeys)->get();

        foreach ($courses as $course) {
            if (!$this->orderService->isBeforeDeadline($course)) {
                return response()->json($this->getPastDeadlineError($course), 400);
            }
        }

        $requiredSeats = (int)$request->input('seats', 1);

        // seats are required, so do NOT use a default value
        if ($this->orderService->reserveSeats($order, $requiredSeats, $courses)) {
            // Sends an event to update the course, if needed
            $this->updateCourses($order);

            return response()->json(new OrderResource($order));
        }

        return response()->json($this->getNotEnoughSeatsError($requiredSeats, $courses, $order), 400);
    }

    /**
     * Fetches a nice error message, saying that there are not enough seats left to reserve.
     * @param int $requiredSeats the number of required seats
     * @param Collection $courses
     * @param Order|null $order the current order (if any)
     * @return array
     */
    private function getNotEnoughSeatsError(int $requiredSeats, Collection $courses, Order $order = null): array
    {
        // Sends an event to update the course, if needed
        $this->updateCourses($order);

        $coursesWithErrors = [];

        // finds "the courses" that doesn't have enough seats available
        $seatsAvailable = 0;
        /** @var Course $course */
        foreach ($courses as $course) {
            if ($course->seats_available < $requiredSeats) {
                // used for setting a general number
                $seatsAvailable = $course->getAvailableSeats($order);

                $coursesWithErrors[] = (object) [
                    'maconomy_id' => $course->maconomy_id,
                    'seats_available' => $seatsAvailable,
                ];
            }
        }

        return [
            'error' => 'Not enough seats available',
            'seats_required' => $requiredSeats,
            'seats_available' => $seatsAvailable,
            'courses_with_error' => $coursesWithErrors
        ];
    }

    /**
     * Updates the courses on the given order, if there are no available seats left
     * @param Order $order
     */
    private function updateCourses(Order $order): void
    {
        // TODO: this doesn't work, when $this->create is called, and the sum goes from 1 -> 0
        /** @var Course $course */
        foreach ($order->courses as $course) {
            // no seats available? tell wordpress to resync the course, to bust the cache in their end
            if ($course->getAvailableSeats($order) <= 0) {
                Queue::later(1, new ImportCourses($course->maconomy_id));
            }
        }
    }

    /**
     * Fetches the error message to send, if the course is past the booking deadline
     * @param Course $course
     * @return array
     */
    private function getPastDeadlineError(Course $course): array
    {
        return [
            'error' => 'Past deadline',
            'deadline' => $course->deadline,
        ];
    }

    /**
     * Closes the order with the given id
     * @param Request $request
     * @param string $id the order id to close
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function closeOrder(Request $request, $id)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $this->validate($request, [
            'participants' => 'required',
            'company' => 'required'
        ]);

        // fails if the order is past deadline for signups
        foreach ($order->courses as $course) {
            if (!$this->orderService->isBeforeDeadline($course)) {
                return response()->json($this->getPastDeadlineError($course), 400);
            }
        }

        $company = $request->input('company', []);
        $participants = $request->input('participants', []);

        // closes the order
        $this->orderService->closeOrder($order, $participants, $company);

        $order->refresh();

        // syncing the order to maconomy
        // TODO: reenable this, when maconomy works
//        Queue::later(1, new SyncOrder($order));

        return response()->json(new OrderResource($order));
    }
}
