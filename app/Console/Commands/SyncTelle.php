<?php

namespace App\Console\Commands;

use App\Notifier;
use App\BilcoDataSerah;
use App\Http\Controllers\API\BilcoDataSerahController;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Carbon\Carbon;
use App\Importer;

class SyncTelle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synctelle {date}';
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

    public function CheckDir
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    }
}
