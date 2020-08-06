<?php

namespace App\Console\Commands;

use App\BilcoDataSerah;
use App\Http\Controllers\API\BilcoDataSerahController;
use Illuminate\Console\Command;

class SyncBilcoDataSerah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcoDataSerah {periode}';
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
     * @return int
     */
    public function handle()
    {
        $x = array();
        $controller = new BilcoDataSerahController();
        foreach($controller->fetch() as $row){
            $i = array();
            $i['account_number'] = $row->account_number;
            $i['msisdn'] = $row->msisdn;
            $i['bill_cycle'] = $row->bill_cycle;
            $i['region'] = $row->regional;
            $i['bucket_4'] = $row->bucket_4;
            $i['bucket_3']= $row->bucket_3;
            $i['bucket_2']= $row->bucket_2;
            $i['bucket_1']= $row->bucket_1;
            $i['import_batch']= 99;
            $i['total_outstanding'] = $row->bucket_4 + $row->bucket_3 + $row->bucket_2 + $row->bucket_1;
            $x[] = $i;
        }
        $arr = collect($x);
        $chunks = $arr->chunk(500);
        foreach ($chunks as $chunk)
        {
            BilcoDataSerah::insert($chunk->toArray());
        }

        $this->info($this->argument('periode'));
        return 0;
    }
}
