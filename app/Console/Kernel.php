<?php

namespace oceler\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \oceler\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      /*
        $schedule->command('inspire')
                 ->hourly();
      */

     $file_path = env('CRON_OUTPUT_LOG', '')

        $schedule->call(function () {
          \oceler\MTurk::testConnection();
        })->everyMinute()
        ->appendOutputTo($filePath);

        /*
        $schedule->call(function () {
          \oceler\MTurk::processAssignments();
        })->everyMinute();

        $schedule->call(function () {
          \oceler\MTurk::processBonus();
        })->everyFiveMinutes();

        $schedule->call(function () {
          \oceler\MTurk::processQualification();
        })->everyFiveMinutes();
        */
    }
}
