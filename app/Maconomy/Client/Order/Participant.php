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

    /**
     * Participant constructor.
     * @param array $data the data to set on the participant
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
        return (int)$this->data['externalidField'];
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
}
