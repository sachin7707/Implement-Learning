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
        return (int)$this->data['id'];
    }

    /**
     * Fetches the participant's instance key (maconomy id)
     * @return string
     */
    public function getInstanceKey(): string
    {
        return (string)$this->data['instancekey'];
    }

    /**
     * Sets the participant's instance key
     * @param string $key
     */
    public function setInstanceKey(string $key): void
    {
        $this->data['instancekey'] = $key;
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
