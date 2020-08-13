<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use MongoDB\Driver\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\SyncBilcoDataSerah::class,
        Commands\SyncBilcollection::class,
        Commands\DBBackup::class,
    ];

    protected function scheduleTimezone()
    {
        return 'Asia/Jakarta';
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('SyncBilcollection')
            ->at('08:30')
            ->appendOutputTo(storage_path() . "/logs/mail.recent");
	//$schedule->exec('touch lol.txt')->dailyAt('00:24');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
