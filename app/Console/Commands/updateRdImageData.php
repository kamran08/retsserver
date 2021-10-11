<?php

namespace App\Console\Commands;
use App\Http\Controllers\UpdateController;
use Illuminate\Console\Command;

class updateRdImageData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateRdImageData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check and update rd image data';

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
         
        \Log::info("Calling update RD1 image processing..");
        $ob = new UpdateController();
        return  $ob->updateImageRD_1();
    }
}
