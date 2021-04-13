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
        Commands\imageResizeAndStore::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('command:FetchAndStore')
        //          ->everyTwoMinutes();
        // $schedule->command('command:FeatchAndSrore2')
        //          ->everyTwoMinutes();
        // $schedule->command('command:latLangFetach')
        //          ->everyTwoMinutes();
        // $schedule->command('command:imageResizeAndStore')
        //          ->everyTwoMinutes();
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
