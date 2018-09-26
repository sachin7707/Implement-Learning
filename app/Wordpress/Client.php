<?php

namespace App\Wordpress;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class Client
{
    /** @var string $url the base url to call */
    private $baseUrl;
    /** @var \GuzzleHttp\Client */
    private $client;

    /**
     * Client constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Calls wordpress asynchronously to tell it to sync all course data again
     */
    public function syncAll()
    {
        $this->getClient()->getAsync('sync_all');
    }

    /**
     * Calls wordpress asynchronously to tell it to sync a single course's data again
     */
    public function syncSingle(string $id)
    {
        $this->getClient()->getAsync("sync/$id");
    }

    /**
     * initializes and returns the http client
     * @return \GuzzleHttp\Client
     */
    private function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        // use bearer with guzzle: https://stackoverflow.com/a/38370987/4873825

        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
        return $this->client;
    }
}