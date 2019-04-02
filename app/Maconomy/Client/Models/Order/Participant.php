<?php

namespace App\Maconomy\Client\Order;

/**
 * A simple participant object, that hold all data in an array, for ease of updating.
 * @author jimmiw
 * @since 2018-11-19
 */
class Participant
{
    /** @var array */
    private $data;
    /** @var int */
    private $courseId;

    /**
     * Participant constructor.
     * @param array $data the data to set on the participant
     * @param int $courseId
     */
    public function __construct(array $data, int $courseId)
    {
        $this->data = $data;
        $this->courseId = $courseId;
    }

    /**
     * Fetches the current data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * fetches the db id of the participant
     * @return int
     */
    public function getId(): int
    {
        $idSplit = explode('.', $this->data['externalidField']);
        return $idSplit[1] ?? $this->data['externalidField'];
    }

    /**
     * Fetches the participant's instance key (maconomy id)
     * @return string
     */
    public function getMaconomyId(): string
    {
        return (string)$this->data['instancekeyField'];
    }

    /**
     * Sets the participant's instance key
     * @param string $key
     */
    public function setMaconomyId(string $key): void
    {
        $this->data['instancekeyField'] = $key;
    }

    /**
     * Checks if the participant has a maconomy id
     * @return bool true if a maconomy id is present
     */
    public function hasMaconomyId(): bool
    {
        return !empty($this->data['instancekeyField']);
    }

    /**
     * Fetches the course number
     * @return string
     */
    public function getCourseNumber(): string
    {
        return (string)$this->data['coursenumber'];
    }

    /**
     * Fetches the course id, the order participant belongs to
     * @return int
     */
    public function getCourseId(): int
    {
        return $this->courseId;
    }
}
