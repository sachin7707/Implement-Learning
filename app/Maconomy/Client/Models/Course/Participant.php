<?php

namespace App\Maconomy\Client\Models\Course;

/**
 * @author jimmiw
 * @since 2019-04-02
 */
class Participant
{
    private $maconomyId;
    private $name;
    private $title;
    private $email;
    private $phone;
    private $companyName;

    /**
     * Participant constructor.
     * @param string $maconomyId
     * @param string $name
     * @param string $email
     * @param string $companyName
     * @param string $phone
     * @param string $title
     */
    public function __construct(string $maconomyId, string $name, string $email, string $companyName, string $phone, string $title)
    {
        $this->maconomyId = $maconomyId;
        $this->name = $name;
        $this->email = $email;
        $this->companyName = $companyName;
        $this->phone = $phone;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMaconomyId(): string
    {
        return $this->maconomyId;
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
}
