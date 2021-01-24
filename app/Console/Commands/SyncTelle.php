<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use File;
use Rap2hpoutre\FastExcel\FastExcel;


class SyncTelle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synctelle';
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

    public function checkDir(){
        /*$xlsxs = Storage::disk('telle')->allFiles();
        foreach($xlsxs as $xlsx){
            dd(
                (new FastExcel)->import(Storage::disk('telle')->get($xlsx),function ($line) {
                    return $line;
                }
                );
            );

        }*/

        return Storage::disk('telle')->allFiles();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        var_dump($this->checkDir());
    }
}
