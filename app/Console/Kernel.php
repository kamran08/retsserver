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
        
        
        Commands\latLangFetach::class,
        Commands\imageResizeAndStore::class,
        Commands\updateRaData::class,
        Commands\updateRdData::class,
        Commands\updateRaImageData::class,
        Commands\updateRdImageData::class,
        Commands\FeatchRaDataByOffset::class,
        Commands\FeatchRdDataByOffset::class,
        Commands\SendExistanceImages::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:SendExistanceImages')
                 ->everyTenMinutes();
        // featching services
        $schedule->command('command:latLangFetach')
                 ->everyMinute();
        
        


        $schedule->command('command:imageResizeAndStore')
                 ->everyFiveMinutes();
        
        $schedule->command('command:FeatchRaDataByOffset')
                 ->everyFiveMinutes();
        $schedule->command('command:FeatchRdDataByOffset')
                 ->everyFiveMinutes();  

        //  update 
        $schedule->command('command:updateRaData')->everyFiveMinutes();
        $schedule->command('command:updateRdData')->everyFiveMinutes();


        $schedule->command('command:updateRaImageData')->everyFiveMinutes();
        $schedule->command('command:updateRdImageData')->everyFiveMinutes();
                 
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
    protected function bootstrappers()
{
    return array_merge(
        [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
        parent::bootstrappers(),
    );
}
}
