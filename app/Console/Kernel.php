<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    // protected $commands = [
    //     //
    // ];
    protected $commands = [
        Commands\FetchAndStore::class,
        Commands\FeatchAndSrore2::class,
        Commands\latLangFetach::class,
        Commands\imageResizeAndStore::class,
        Commands\CheckUpdatedData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:FetchAndStore')
                 ->everyFiveMinutes();
        $schedule->command('command:FeatchAndSrore2')
                 ->everyFiveMinutes();
        $schedule->command('command:latLangFetach')
                 ->everyMinute();
        $schedule->command('command:imageResizeAndStore')
                 ->everyFiveMinutes();
        $schedule->command('command:CheckUpdatedData')
                 ->everyFiveMinutes();
        $schedule->command('command:CheckUpdatedData2')
                 ->everyFiveMinutes();
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
