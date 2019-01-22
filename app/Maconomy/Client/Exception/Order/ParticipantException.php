<?php

namespace App\Maconomy\Client\Exception\Order;

/**
 * @author jimmiw
 * @since 2019-01-22
 */
class ParticipantException extends \Exception
{
    /**
     * ParticipantException constructor.
     * @param string $message
     * @param array $data
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct($message, $data, $code = 0, $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Fetches the data that was sent to the server, during the request
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
