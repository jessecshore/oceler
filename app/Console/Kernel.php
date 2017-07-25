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

        $schedule->call(function () {
          \oceler\MTurk::processAssignments();
        })->hourly();

        $schedule->call(function () {
          \oceler\MTurk::processBonus();
        })->cron('5 * * * *');

        $schedule->call(function () {
          \oceler\MTurk::processQualification();
        })->cron('10 * * * *');

    }
}
