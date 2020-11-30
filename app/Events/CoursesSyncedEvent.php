<?php

namespace App\Events;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class CoursesSyncedEvent extends Event
{
    /** @var string  */
    public $id;

    /**
     * @param string $id the course id that was synced, if none, just leave this empty
     */
    public function __construct(string $id = '')
    {
        $this->id = $id;
    }
}