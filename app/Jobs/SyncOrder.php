<?php

namespace App\Jobs;

use App\Maconomy\Client\Maconomy;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-11-19
 */
class SyncOrder extends Job
{
    /**
     * SyncOrder constructor.
     * @param Order $order the order to sync
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Handle the sync with the webservice (maconomy)
     * @param Maconomy $client
     */
    public function handle(Maconomy $client)
    {
        $client->setOrder($this->order);

        if ($this->order->state === Order::STATE_CONFIRMED) {
            $response = $client->orderUpdate();
        } else {
            $response = $client->orderCreate();
        }
    }
}
