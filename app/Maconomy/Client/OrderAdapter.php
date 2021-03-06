<?php

namespace App\Maconomy\Client;

use App\Company;
use App\Course;
use App\Maconomy\Client\Order\ParticipantAdapter;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-11-19
 */
class OrderAdapter
{
    /** @var ParticipantAdapter[] $participants holds the list of participant data we need */
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
                $participant->setMaconomyId($instanceKey);
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
                // This is done to fix a bug in ILI-717, where maconomy cannot understand the same participant id
                // is used for multiple courses on a single order.
                $participantMaconomy = $dbParticipant->getMaconomyByCourse($course->id);

                $participants[] = new ParticipantAdapter([
                    // sending our internal id with as well, so we can update db after ws-sync
                    // NOTE: this field is crucial, since it decides if a participant is created or updated
                    'externalidField' => $course->id .'.'.$dbParticipant->id,
                    // instancekeyField is used, if you wish to change data about a participant (should not be needed?)
                    'instancekeyField' => $participantMaconomy ? $participantMaconomy->maconomy_id : '',
                    'packageidField' => $this->order->education->maconomy_id ?? '',
                    'coursenumberField' => $course->maconomy_id,
                    'waitlistField' => (bool)$this->order->on_waitinglist,
                    // participant information
                    "participantnameField" => $dbParticipant->name,
                    "participantemailField" => $dbParticipant->email,
                    "participantphoneField" => $dbParticipant->phone,
                    "participanttitleField" => $dbParticipant->title,
                    // company information
                    "compnameField" => $company->name,
                    "compcvrField" => $company->cvr,
                    "compattField" => $company->attention,
                    "compaddressField" => $company->address,
                    "compzipcodeField" => $company->postal,
                    "compcityField" => $company->city,
                    "compcountryField" => $company->country,
                    "compemailField" => $company->email,
                    "compphoneField" => $company->phone,
                    "compeanField" => $company->ean,
                    "comppurchaseorderField" => $company->purchase_no,
                    // rest of the fields are used, if the billing address is not the same.
                    "altcompnameField" => $company->billing_name ?? '',
                    "altcompattField" => $company->billing_attention ?? '',
                    "altcompaddressField" => $company->billing_address ?? '',
                    "altcompzipcodeField" => $company->billing_postal ?? '',
                    "altcompcityField" => $company->billing_city ?? '',
                    "altcompcountryField" => $company->billing_country ?? '',
                    "altcompemailField" => $company->billing_email ?? '',
                    "altcompphoneField" => $company->billing_phone ?? '',
                ], $course->id);
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
     * @throws \Exception
     */
    public function markAsSynced()
    {
        $this->order->state = Order::STATE_CONFIRMED;
        $this->order->last_sync_date = new \DateTime();
        $this->order->save();
    }
}
