<?php

namespace App\Console\Commands;
use App\Http\Controllers\UpdateController;
use Illuminate\Console\Command;

class updateRaImageData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateRaImageData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update RA_2 image process';

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
        \Log::info("Calling update Ra2 image processing..");
        $ob = new UpdateController();
        return  $ob->updateImageRA_2();

    }
}
