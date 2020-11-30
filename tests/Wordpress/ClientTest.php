<?php

/**
 * @author jimmiw
 * @since 2018-10-04
 */
class ClientTest extends TestCase
{
    public function testAlive()
    {
        $client = $this->getClient();

        static::assertInstanceOf(\App\Wordpress\Client::class, $client);
    }

    public function testSyncAllOk()
    {
        $client = $this->getClient();

        $response = $client->syncAll();
        static::assertNotEmpty($response);
    }

    public function testSyncSingle()
    {
        $client = $this->getClient();

        $response = $client->syncSingle(123);
        static::assertNotEmpty($response);
    }

    /**
     * @return \App\Wordpress\Client
     */
    private function getClient(): \App\Wordpress\Client
    {
        return new App\Wordpress\Client(env('WORDPRESS_API_URL'));
    }
}
