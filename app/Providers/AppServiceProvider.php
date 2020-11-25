<?php

namespace App\Providers;

use App\Log\Job;
use Carbon\Carbon;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            Job::updateOrCreate([
                'job_id' => $event->job->getJobId(),
            ], [
                'name' => $event->job->resolveName(),
                'queue_name' => $event->job->getQueue(),
                'start_time' => Carbon::now(),
                'data' => $event->job->getRawBody(),
                'status' => 'started',
            ]);
        });

        Queue::after(function (JobProcessed $event) {
            Job::updateOrCreate([
                'job_id' => $event->job->getJobId(),
            ], [
                'end_time' => Carbon::now(),
                'status' => 'completed',
            ]);
        });

        Queue::failing(function (JobFailed $event) {
            Job::updateOrCreate([
                'job_id' => $event->job->getJobId(),
            ], [
                'end_time' => Carbon::now(),
                'status' => 'failed',
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
