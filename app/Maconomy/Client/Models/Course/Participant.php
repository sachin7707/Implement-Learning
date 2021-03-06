<?php

namespace App\Maconomy\Client\Models\Course;

/**
 * Participant class, for handling participants from maconomy api.
 * We implement the mail participant interface, to be usable in various mails.
 * @author jimmiw
 * @since 2019-04-02
 */
class Participant implements \App\Mail\Adapters\Participant
{
    /** @var bool $onWaitingList normally false, meaning a participant is signed up. Can be false as well */
    private $onWaitingList = false;
    private $name;
    private $title;
    private $email;
    private $phone;
    private $companyName;

    /**
     * Participant constructor.
     * @param string $name
     * @param string $email
     * @param string $companyName
     * @param string $phone
     * @param string $title
     */
    public function __construct(string $name, string $email, string $companyName, string $phone, string $title)
    {
        $this->name = $name;
        $this->email = $email;
        $this->companyName = $companyName;
        $this->phone = $phone;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * Marks the participant as being on the waiting list
     */
    public function setIsOnWaitingList()
    {
        $this->onWaitingList = true;
    }

    /**
     * Checks if the current participant is on the waiting list
     * @return bool
     */
    public function isOnWaitingList()
    {
        return $this->onWaitingList;
    }
}
