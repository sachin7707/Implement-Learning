<?php

namespace App\Maconomy\Client;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Order
{
    /** @var int */
    private $id;

    /**
     * Order constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }
}