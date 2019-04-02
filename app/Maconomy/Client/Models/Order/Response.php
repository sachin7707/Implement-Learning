<?php

namespace App\Maconomy\Client\Order;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class Response implements \App\Maconomy\Client\Response
{
    /** @var string $response */
    private $response;

    /**
     * Response constructor.
     * @param string $responseString
     */
    public function __construct(string $responseString)
    {
        $this->response = $responseString;
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->response;
    }
}