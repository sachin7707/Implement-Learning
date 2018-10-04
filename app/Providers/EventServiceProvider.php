<?php

namespace App\Providers;

use App\Events\CoursesSyncedEvent;
use App\Listeners\NotifyCoursesSynced;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CoursesSyncedEvent::class => [NotifyCoursesSynced::class]
    ];
}
