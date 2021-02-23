<?php

namespace App\Console;

use App\Jobs\ArtisanCommandJob;
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
        $schedule->job(new ArtisanCommandJob('timestamps:sanitize'))->daily()->onOneServer()->name('timestamp_sanitizer');
        $schedule->job(new ArtisanCommandJob('timesheet:generate'))->monthlyOn(1, '0:00')->onOneServer()->name('generate_timesheet_real');
        $schedule->job(new ArtisanCommandJob('timesheet:generate', ['--target' => true]))->monthlyOn(1, '0:00')->onOneServer()->name('generate_timesheet_target_time');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
