<?php

namespace App\Maconomy\Client;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
interface Response
{
    /**
     * @return string
     */
    public function getRaw(): string;
}
