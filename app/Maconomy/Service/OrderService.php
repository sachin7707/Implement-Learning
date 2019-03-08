<?php

namespace App\Maconomy\Service;

use App\Company;
use App\Course;
use App\Jobs\SyncOrder;
use App\Mail\Helper;
use App\Mail\OrderBooker;
use App\Mail\OrderParticipant;
use App\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

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
        $coursesWithSeatsUnavailable = 0;

        // if the course is set as a waitinglist order, just skip seat checks
        if ((int)$order->on_waitinglist === 0) {
            if (!empty($courses)) {
                /** @var Course $course */
                foreach ($courses as $course) {
                    // updates the seats available from maconomy
                    $this->courseService->updateSeatsAvailable($course);

                    // fetching the number of seats available on the course (without the currently reserved seats)
                    if ($course->getAvailableSeats($order) < $requiredSeats) {
                        // NOTE: this method should still be called though, else system is not updated properly - ILI-380
                        $seatsAreAvailable = false;
                        $coursesWithSeatsUnavailable++;
                    }
                }
            }

            // all courses have no seats available, set the order as a waitinglist order
            if ($seatsAreAvailable === false && $coursesWithSeatsUnavailable === count($courses)) {
                // the status can only be set, if there are no existing courses on the order.
                if (count($order->courses) === 0) {
                    $order->on_waitinglist = 1;
                    // updating seats available, so we can save the data on the order and continue
                    $seatsAreAvailable = true;
                }
            }
        }

        // if we can, reserve the seats!
        // NOTE: if we get to this step, just reserve the seats? deadline checks have already been done - ILI-380
        if ($seatsAreAvailable) {
            $order->seats = $requiredSeats;
            // removing existing courses on the order
            DB::table('course_order')->where('order_id', '=', $order->id)->delete();
            // adding the courses to be used
            $order->courses()->saveMany($courses);
            $order->save();

            return true;
        }

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
     * Checks if the given course is still signupable, by looking at the course start time
     * @param Course $course the course to check
     * @return bool
     */
    public function isBeforeStartDate(Course $course): bool
    {
        // fetching "now"
        $now = new \DateTime('now', new \DateTimeZone('GMT'));

        $courseStart = new \DateTime($course->start_time, new \DateTimeZone('GMT'));
        return $courseStart > $now->format('Y-m-d H:i:s');
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
            // creates a new empty company attached to the order
            $order->company()->create();
        }

        // refetching the order, else the company will no work
        $order->refresh();

        /** @var Company $company saving the company on the order*/
        $order->company->update($companyDetails);
        $company = $order->company;

        // removing "old" participants
        DB::table('participants')->where('company_id', '=', $company->id)->delete();

        foreach ($participants as $participant) {
            $company->participants()->create($participant);
        }

        $this->sendOrderEmails($order);

        // syncing the order to maconomy
        Queue::later(1, new SyncOrder($order));
    }

    /**
     * Sends the emails, for the given order
     * @param Order $order
     */
    public function sendOrderEmails(Order $order): void
    {
        $orderMail = Helper::getMailer($order->company->email, true);
        $orderMail->queue(new OrderBooker($order));

        // queues the mails to the participants
        foreach ($order->company->participants as $participant) {
            $participantMail = Helper::getMailer($participant->email, false);
            // queues the mail to the booker
            $participantMail->queue(new OrderParticipant($order, $participant));
        }
    }
}
