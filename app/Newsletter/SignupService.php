<?php

namespace App\Newsletter;

use GuzzleHttp\Client;

/**
 * Sends information to implements clickdimension service, to add user's to the newsletter
 * @author jimmiw
 * @since 2019-02-06
 */
class SignupService implements NewsletterService
{
    /** @var Client */
    private $client;
    /** @var string  */
    private $url;

    /**
     * SignupService constructor.
     * @param string $url the url to send the signup information to
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Signs a user up for the newsletter, using the given data
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return mixed the response from the server
     */
    public function signup(string $firstname, string $lastname, string $email)
    {
        $client = $this->getClient();
        $response = $client->post($this->url, [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'terms' => 1
        ]);

        return json_decode((string)$response->getBody());
    }

    /**
     * Fetches the actual soap client to use.
     * @return Client
     */
    private function getClient(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client();

        return $this->client;
    }
}
