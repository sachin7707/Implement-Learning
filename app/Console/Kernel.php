<?php

namespace App\Console;

use App\Jobs\ClearReservations;
use App\Jobs\ImportCourses;
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
        //
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
        })->description('ImportCourses')->daily();

        $schedule->call(function () {
            $job = new ClearReservations();
            dispatch($job);
        })->description('ClearReservations')->cron('* * * * *');
        // broken in 5.7.1
        //$schedule->job(new ClearReservations())->everyFiveMinutes();
    }
}
