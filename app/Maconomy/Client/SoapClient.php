<?php

namespace App\Maconomy\Client;

/**
 * General interface for soap clients :)
 */
interface SoapClient
{
    /**
     * Checks if the given response is valid
     * @return Response the response sent from the server
     */
    public function orderCreate(): Response;

    /**
     * Updates the given order
     * @return Response
     */
    public function orderUpdate(Order $data): Response;

    /**
     * Deletes the order with the given id on the server
     * @param int $id the order id to delete
     * @return Response
     */
    public function orderDelete(int $id): Response;
}
