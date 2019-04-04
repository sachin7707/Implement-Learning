<?php

namespace App\Mail\Adapters;

/**
 * Interface for participant objects, that is used when sending emails.
 * @author jimmiw
 * @since 2019-04-04
 */
interface Participant
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getPhone(): string;

    /**
     * @return string
     */
    public function getCompanyName(): string;
}