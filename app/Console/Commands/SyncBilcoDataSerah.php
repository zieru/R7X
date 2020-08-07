<?php

namespace App\Console\Commands;

use App\BilcoDataSerah;
use App\Http\Controllers\API\BilcoDataSerahController;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

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
        echo 'Query started';
        $cx= $controller->fetch();
        $this->info('Query finished');
        foreach($cx as $row){
            $i =  (array) $row;
            $i['cek_cp'] = false;
            $i['cek_halo'] = false;

            if($row->customer_phone){
                $ret = $row->customer_phone;
                switch(substr($row->customer_phone, 0, 2)){
                    case '08' :
                        $ret = '628'. $ret;
                        break;
                    case 62:
                        $ret = $ret;
                        break;
                    default:
                        $ret = '62'. $ret;
                }
            }
            if($row->customer_phone){
                $i['cek_cp'] = true;

                if($ret == $row->msisdn){
                    $i['cek_halo'] = true;
                }
            }

            /*$i['account'] = $row->account_number;
            $i['peride'] = $row->account_periode;
            $i['msisdn'] = $row->msisdn;
            $i['bill_cycle'] = $row->bill_cycle;
            $i['region'] = $row->regional;
            $i['bucket_4'] = $row->bucket_4;
            $i['bucket_3']= $row->bucket_3;
            $i['bucket_2']= $row->bucket_2;
            $i['bucket_1']= $row->bucket_1;

            */
            $i['import_batch']= 99;
            $i['total_outstanding'] = $row->bucket_4 + $row->bucket_3 + $row->bucket_2 + $row->bucket_1;
            $x[] = $i;
        }
        $this->info('Writing to table serah');
        $arr = collect($x);
        $chunks = $arr->chunk(500);
        foreach ($chunks as $chunk)
        {
            BilcoDataSerah::insert($chunk->toArray());
        }

        $this->info('Done');
        $this->info($this->argument('periode'));
        return 0;
    }
}
