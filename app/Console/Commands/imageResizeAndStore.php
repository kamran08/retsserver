<?php

namespace App\Console\Commands;

use App\Http\Controllers\RetsController;
use Illuminate\Console\Command;

class imageResizeAndStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:imageResizeAndStore';

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
        \Log::info("image resize and store");
        $ob = new RetsController();
        return  $ob->storeImages();
        //
    }
}
