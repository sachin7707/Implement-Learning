<?php

namespace App\Mail\Adapters;

use App\Participant as ParticipantModel;

/**
 * Adapter for the participant eloquent model, to have the same methods as the participant interface, so we
 * easily can use the eloquent model, without adding more methods to it.
 * @author jimmiw
 * @since 2019-04-04
 */
class ParticipantAdapter implements Participant
{
    private $participant;

    /**
     * ParticipantModelAdapter constructor.
     * @param ParticipantModel $participant
     */
    public function __construct(ParticipantModel $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->participant->name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->participant->title;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->participant->email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->participant->phone;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->participant->company->name ?? '';
    }
}
