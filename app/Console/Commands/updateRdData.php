<?php

namespace App\Console\Commands;
use App\Http\Controllers\UpdateController;
use Illuminate\Console\Command;

class updateRdData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateRdData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
          
          \Log::info("Calling update Rd1 data");
          $ob = new UpdateController();
          return  $ob->updateRD_1Data();
          return  $ob->SendImagesToMainServer();
    }
}
