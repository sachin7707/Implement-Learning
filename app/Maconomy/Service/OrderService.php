<?php

namespace App\Maconomy\Service;

use App\Company;
use App\Course;
use App\Order;
use App\Participant;
use Illuminate\Support\Facades\DB;

/**
 * @author jimmiw
 * @since 2018-10-02
 */
class OrderService
{
    /** @var CourseService */
    private $courseService;

    /**
     * OrderService constructor.
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Reserves the given number of seats, on the given order.
     * @param Order $order
     * @param int $requiredSeats
     * @param array $courses
     * @return bool false if the seats could not be reserved, else true
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reserveSeats(Order $order, int $requiredSeats, $courses): bool
    {
        $seatsAreAvailable = true;
        if (! empty($courses)) {
            /** @var Course $course */
            foreach ($courses as $course) {
                // updates the seats available from maconomy
                $this->courseService->updateSeatsAvailable($course);

                // fetching the number of seats available on the course (without the currently reserved seats)
                if ($course->getAvailableSeats($order) < $requiredSeats) {
                    // NOTE: this method should still be called though, else system is not updated properly - ILI-380
                    $seatsAreAvailable = false;
                }
            }
        }

        // if we can, reserve the seats!
        // NOTE: if we get to this step, just reserve the seats? deadline checks have already been done - ILI-380
//        if ($seatsAreAvailable) {
            $order->seats = $requiredSeats;
            // removing existing courses on the order
            DB::table('course_order')->where('order_id', '=', $order->id)->delete();
            // adding the courses to be used
            $order->courses()->saveMany($courses);
            $order->save();

            return true;
//        }

        return false;
    }

    /**
     * Checks if the given course is still signupable, by looking at the signup deadline
     * @param Course $course the course to check
     * @return bool
     */
    public function isBeforeDeadline(Course $course): bool
    {
        if ($course->deadline === null) {
            return true;
        }

        // fetching "now"
        $now = new \DateTime('now', new \DateTimeZone('GMT'));

        return $course->deadline > $now->format('Y-m-d H:i:s');
    }

    /**
     * Closes the given order, marking it as ready for sync with maconomy.
     * @param Order $order the order to close and save participants and company info on.
     * @param array $participants the list of participants to add to the order
     * @param array $companyDetails the company information
     */
    public function closeOrder(Order $order, array $participants, array $companyDetails): void
    {
        $order->state = Order::STATE_CLOSED;
        $order->save();

        if ($order->company === null) {
            $order->company()->create();
        }

        // refetching the order, else the company will no work
        $order->refresh();

        // saving the company on the order
        /** @var Company $company */
        $order->company->update($companyDetails);
        $company = $order->company;

        // removing "old" participants
        DB::table('participants')->where('company_id', '=', $company->id)->delete();

        foreach ($participants as $participant) {
            $company->participants()->create($participant);
        }

        // TODO: send the order data to maconomy... later?
    }
}
