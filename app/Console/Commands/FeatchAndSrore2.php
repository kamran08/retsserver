<?php

namespace App\Console\Commands;

use App\Http\Controllers\RetsController;
use Illuminate\Console\Command;

class FeatchAndSrore2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:FeatchAndSrore2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Featch RD_1 Data and sotre';

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
        \Log::info("one");
        $ob = new RetsController();
        return  $ob->featchRdData();
    }
}
