<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use File;


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
