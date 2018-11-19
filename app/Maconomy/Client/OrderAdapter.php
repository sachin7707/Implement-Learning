<?php

namespace App\Maconomy\Client;

use App\Company;
use App\Course;
use App\Maconomy\Client\Order\Participant;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-11-19
 */
class OrderAdapter
{
    /** @var Participant[] $participants holds the list of participant data we need */
    private $participants;
    /** @var Order $order the current order */
    private $order;

    /**
     * OrderAdapter constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        // initializing the participants here, since they can be manipulated and returned later on...
        $this->initParticipants();
    }

    /**
     * Updates the participant with the given id and maconomy course id, to have the new instance key
     * @param int $id the participant's db id
     * @param string $courseMaconomyId the course's maconomy id
     * @param string $instanceKey the new instance key to save to the participant
     */
    public function updateParticipant(int $id, string $courseMaconomyId, string $instanceKey): void
    {
        foreach ($this->participants as $participant) {
            if ($participant->getId() === $id && $participant->getCourseNumber() === $courseMaconomyId) {
                // updating the participant's instance key
                $participant->setInstanceKey($instanceKey);
                // stops the loop, since we found what we needed
                break;
            }
        }
    }

    /**
     * initializes the participant data
     */
    private function initParticipants(): void
    {
        $participants = [];

        /** @var Company $company */
        $company = $this->order->company;

        /** @var Course $course runs through each course, to create participants for each */
        foreach ($this->order->courses as $course) {
            /** @var \App\Participant $dbParticipant */
            foreach ($company->participants as $dbParticipant) {
                $participants[] = new Participant([
                    // sending our internal id with as well, so we can update db after ws-sync
                    'id' => (int)$dbParticipant->id,
                    // TODO: handle multiple courses/instance keys!
                    'instancekey' => $dbParticipant->maconomy_id,
                    'coursenumber' => $course->maconomy_id,
                    'contactpersonnumber' => '',
                    'name1' => $dbParticipant->name,
                    'name2' => $dbParticipant->title,
                    'name3' => '',
                    'name4' => '',
                    'name5' => '',
                    'zipcode' => '',
                    'postaldistrict' => '',
                    'email' => $dbParticipant->email,
                    'phone' => $dbParticipant->phone,
                    'compname1' => $company->name,
                    'compname2' => $company->attention,
                    'compname3' => $company->address,
                    'compname4' => '',
                    'compname5' => '',
                    'compzipcode' => $company->postal,
                    'comppostaldistrict' => $company->city,
                    'compemail' => $company->email,
                    'compphone' => $company->phone,
                    'cvr' => $company->cvr,
                    'contactcompanynumber' => '',
                    'packageid' => $this->order->education->maconomy_id
                ]);
            }
        }

        $this->participants = $participants;
    }

    /**
     * Fetches the participants with the data needed for the webservice
     * @return array list of Participants
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    /**
     * Marks the order as sync'ed in our local database
     */
    public function markAsSynced()
    {
        $this->order->state = Order::STATE_CONFIRMED;
        $this->order->save();
    }
}
