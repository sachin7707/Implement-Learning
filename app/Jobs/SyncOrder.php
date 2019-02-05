<?php

namespace App\Jobs;

use App\Maconomy\Client\Maconomy;
use App\Maconomy\Client\OrderAdapter;
use App\Order;

/**
 * @author jimmiw
 * @since 2018-11-19
 */
class SyncOrder extends Job
{
    /** @var Order $order the order to sync to the service */
    private $order;

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
     * @throws \App\Maconomy\Client\Exception\NoOrderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \App\Maconomy\Client\Exception\Order\ParticipantException
     */
    public function handle(Maconomy $client)
    {
        // sets the order on the client, but wrapping it in the adapter first
        $client->setOrder(new OrderAdapter($this->order));
        $client->orderCreate();
    }
}
