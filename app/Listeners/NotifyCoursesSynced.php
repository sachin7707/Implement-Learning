<?php

namespace App\Listeners;

use App\Wordpress\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class NotifyCoursesSynced implements ShouldQueue
{
    /** @var Client */
    private $client;

    /**
     * NotifyCoursesSynced constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a notification about re-syncing a single or multiple courses again
     * @param string $courseId
     */
    public function handle(string $courseId)
    {
        error_log('NotifyCoursesSynced');
        if (! empty($courseId)) {
            $this->client->syncSingle($courseId);
            return;
        }

        $this->client->syncAll();
    }
}