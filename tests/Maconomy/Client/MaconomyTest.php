<?php

use PHPUnit\Framework\TestCase;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class MaconomyTest extends TestCase
{
    public function testInit()
    {
        $client = new \App\Maconomy\Client\Maconomy('','');
        $this->assertInstanceOf(\App\Maconomy\Client\SoapClient::class, $client);
    }
}
