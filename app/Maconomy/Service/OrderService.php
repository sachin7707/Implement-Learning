<?php

namespace App\Maconomy\Service;

use App\Company;
use App\Course;
use App\Mail\OrderBooker;
use App\Mail\OrderParticipant;
use App\Order;
use App\Participant;
use Illuminate\Support\Facades\Mail;

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
     * @return bool false if the seats could not be reserved, else true
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reserveSeats(Order $order, int $requiredSeats): bool
    {
        $course = $order->course;
        // updates the seats available from maconomy
        $this->courseService->updateSeatsAvailable($course);

        // fetching the number of seats available on the course (without the currently reserved seats)
        $availableSeats = $course->getAvailableSeats($order);

        // if we can, reserve the seats!
        if ($availableSeats >= $requiredSeats) {
            $order->seats = $requiredSeats;
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
     * @param array $participantsData the list of participants to add to the order
     * @param array $companyData the company information
     * @param array $billingData the billing information, if different from company
     */
    public function closeOrder(Order $order, array $participantsData, array $companyData, array $billingData): void
    {
        // TODO: save participants locally
        // TODO: save company locally

        // if the order has not been closed before the deadline, the seats are set to be on a waiting list - ILI-336
        if (! $this->isBeforeDeadline($order->course)) {
            $order->on_waitinglist = true;
        }

        // sets the order state
        $order->state = Order::STATE_CLOSED;
        $order->save();

        $company = $this->saveCompanyData($order, $companyData, $billingData);
        $this->saveParticipants($company, $participantsData);

        // refreshing the order object
        $order->refresh();

        // queues the mail to the booker
        Mail::to($order->company->email)
            // bcc'ing the mail to implement as well
            ->bcc('ili@implement.dk')
            ->queue(new OrderBooker($order));

        // queues the mails to the participants
        foreach ($order->company->participants as $participant) {
            // queues the mail to the booker
            Mail::to($participant->email)
                ->queue(new OrderParticipant($order, $participant));
        }
    }

    /**
     * Saves the company data
     * @param Order $order
     * @param array $companyData
     * @param array $billingData
     * @return Company the created company
     */
    private function saveCompanyData(Order $order, array $companyData, array $billingData): Company
    {
        $company = new Company();
        $company->name = $companyData['name'];
        $company->cvr = $companyData['cvr'];
        $company->attention = $companyData['attention'];
        $company->address = $companyData['address'];
        $company->postal = $companyData['postal'];
        $company->city = $companyData['city'];
        $company->country = $companyData['country'];
        $company->phone = $companyData['phone'];
        $company->email = $companyData['email'];
        $company->purchase_no = $companyData['purchase_no'];

        // only save billing info, if it's not empty
        if (! empty($billingData)) {
            $company->billing_name = $billingData['name'];
            $company->billing_cvr = $billingData['cvr'];
            $company->billing_attention = $billingData['attention'];
            $company->billing_address = $billingData['address'];
            $company->billing_postal = $billingData['postal'];
            $company->billing_city = $billingData['city'];
            $company->billing_country = $billingData['country'];
            $company->billing_phone = $billingData['phone'];
            $company->billing_email = $billingData['email'];
        }

        $company->order_id = $order->id;
        $company->save();

        return $company;
    }

    /**
     * Saves the participants, using the participantsData array
     * @param Company $company the company to add the participants to
     * @param array $participantsData the data from the post
     */
    private function saveParticipants(Company $company, array $participantsData)
    {
        foreach ($participantsData as $row) {
            $participant = new Participant();
            $participant->name = $row['name'];
            $participant->email = $row['email'];
            $participant->title = $row['title'];
            $participant->phone = $row['phone'];
            $participant->company_id = $company->id;

            $participant->save();
        }
    }
}
