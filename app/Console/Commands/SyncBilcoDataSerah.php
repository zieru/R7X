<?php

namespace App\Console\Commands;

use App\Notifier;
use App\BilcoDataSerah;
use App\Http\Controllers\API\BilcoDataSerahController;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Carbon\Carbon;
use App\Importer;

class SyncBilcoDataSerah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcoDataSerah {date}';
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
        
	//$date = Carbon::now()->subDays(2);
 $date  = Carbon::createFromFormat('Y-m-d', $this->argument('date'));
	Notifier::create([
                    'type' => 'DataSerahImport',
                    'subject' => 'DataSerah Import file',
                    'message' => 'Import dataserah started',
                ]);
        $cx= $controller->fetch($date);
        $this->info('Query finished');
Notifier::create([
                    'type' => 'DataSerahImport',
                    'subject' => 'DataSerah Import processed',
                    'message' => 'Import dataserah processed'
                ]);
$importer  = Importer::create(array(
      'importedRow'=>0,
      'storedRow'=>0,
      'status' => 'QUEUE'
    ));
    $cx->chunk(500000, function($cx) use($x,$importer) {
    $cx->each(function($row) use ($x,$importer) {
            $i =  (array) $row;
            $i['cek_cp'] = false;
            $i['cek_halo'] = false;



            if(strlen(preg_replace('/#^0+|\W#/',null,preg_replace('/^([0-9]|[1-9][0-9])$/', null, preg_replace('/\D/',null,$row->customer_phone)))) >= 6){
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
                if($row->customer_phone){
                $i['cek_cp'] = true;
                if($ret == $row->msisdn){
                    $i['cek_halo'] = true;
                }
                }
            }
            
            if($row->bucket_2 > 0 AND $row->bucket_1 > 0){
              $i['kpi'] = '30-60';
            }
            if($row->bucket_3 > 0 && $row->bucket_2 > 0){
              $i['kpi'] = '60-90';
            }
            if($row->bucket_4 > 0 && $row->bucket_3 > 0){
              $i['kpi'] = '90-120';
            }
            
            $i['import_batch']= $importer->id;
            $i['total_outstanding'] = $row->bucket_4 + $row->bucket_3 + $row->bucket_2 + $row->bucket_1;
           $i;
            BilcoDataSerah::insert($i);
        });
});
/*
    $this->info('Writing to table serahx');
    $x = collect($x);    
    foreach ($x->chunk(10000) as $chunk)
       {
           BilcoDataSerah::insert($chunk->toArray());
       }
       */
$importer->status = "Finish";
    $importer->save();
Notifier::create([
                    'type' => 'DataSerahImport',
                    'subject' => 'DataSerah Import finished',
                    'message' => 'Import dataserah finished'
                ]);
        $this->info('Done');
        return 0;
    }
}
