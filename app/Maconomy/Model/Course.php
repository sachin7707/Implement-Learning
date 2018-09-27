<?php

namespace App\Maconomy\Model;

/**
 * Simple course class, to handle values from maconomy
 * @author jimmiw
 * @since 2018-09-27
 */
class Course
{
    public $id;
    public $startTime;
    public $endTime;
    public $maxParticipants;
    public $currentParticipants;
    public $price;
}