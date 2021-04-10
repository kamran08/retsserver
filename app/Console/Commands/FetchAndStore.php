<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RetsController;

class FetchAndStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:FetchAndStore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch RA_2 data and store';

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
        \Log::info("two");
        $ob = new RetsController();
        // return  $ob->featchRAData();
        
    }
}
