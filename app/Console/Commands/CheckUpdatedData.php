<?php

namespace App\Console\Commands;


use App\Http\Controllers\UpdateController;
use Illuminate\Console\Command;

class CheckUpdatedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CheckUpdatedData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check updated data from rets server and update them.';

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
    public function handle(){
        \Log::info("Calling CheckUpdatedData");
        $ob = new UpdateController();
        return  $ob->updateRa2Data();
    }
}
