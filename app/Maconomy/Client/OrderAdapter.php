<?php

namespace App\Maconomy\Client;

use App\Order;

/**
 * @author jimmiw
 * @since 2018-11-19
 */
class OrderAdapter
{
    /** @var Order $order */
    private $order;

    /**
     * OrderAdapter constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
