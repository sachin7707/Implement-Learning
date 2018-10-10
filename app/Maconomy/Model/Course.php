<?php

namespace App\Maconomy\Model;

/**
 * Simple course class, to handle values from maconomy
 * @author jimmiw
 * @since 2018-09-27
 */
class Course
{
    public $maconomyId;
    public $name;
    public $startTime;
    public $endTime;
    public $maxParticipants;
    public $minParticipants;
    public $seatsAvailable;
    public $seatsTaken;
    public $currentParticipants;
    public $price;
    public $language;
    public $venueId;
    public $venueName;
}