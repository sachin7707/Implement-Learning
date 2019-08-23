<?php

namespace App\Console;

use App\Console\Commands\SyncCourse;
use App\Jobs\ClearReservations;
use App\Jobs\GdprCleanup;
use App\Jobs\ImportCourses;
use App\Jobs\SendParticipantList;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncCourse::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // broken in 5.7.1
        //$schedule->job(new ImportCourses())->daily();

        $schedule->call(function () {
            $job = new ImportCourses();
            dispatch($job);
        })->description('ImportCourses')
            ->hourly();

        $schedule->call(function () {
            $job = new ClearReservations();
            dispatch($job);
        })->description('ClearReservations')->everyFiveMinutes();
        // broken in 5.7.1
        //$schedule->job(new ClearReservations())->everyFiveMinutes();

        $schedule->call(function () {
            $job = new SendParticipantList();
            dispatch($job);
        })->description('SendParticipantList')
            ->timezone('Europe/Copenhagen')
            ->dailyAt('7:00');

        // Order cleanup job, which removes old orders according to GDPR - ILI-451
        $schedule->call(function () {
            $job = new GdprCleanup();
            dispatch($job);
        })->description('GdprCleanup')
            ->timezone('Europe/Copenhagen')
            ->dailyAt('4:00');
    }
}
