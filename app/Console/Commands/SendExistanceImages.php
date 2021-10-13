<?php

namespace App\Console\Commands;
use App\Http\Controllers\UpdateController;

use Illuminate\Console\Command;

class SendExistanceImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendExistanceImages';

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
        //
        $ob = new UpdateController();
        \Log::info("hell");
        return 1;
        return  $ob->SendImagesToMainServer();
    }
}
