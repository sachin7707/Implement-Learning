<?php

namespace App\Newsletter;

/**
 * Interface to use, when signing up for a newsletter
 * @author jimmiw
 * @since 2019-02-06
 */
interface NewsletterService
{
    /**
     * Signs a user up for the newsletter, using the given data
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return mixed the response from the server
     */
    public function signup(string $firstname, string $lastname, string $email);
}
