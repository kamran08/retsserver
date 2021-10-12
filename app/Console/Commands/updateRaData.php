<?php

namespace App\Console\Commands;
use App\Http\Controllers\UpdateController;
use Illuminate\Console\Command;

class updateRaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateRaData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check and update ra_2 data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        \Log::info("Calling update Ra2 data service");
        $ob = new UpdateController();
          $ob->SendImagesToMainServer();
        return  $ob->updateRa2Data();

    }
}
