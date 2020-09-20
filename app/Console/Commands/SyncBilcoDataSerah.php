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
$bilcoenddate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2)->endOfMonth();
$bilcodate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2);
        $tahap = false;
        switch ($bilcodate->format('d')){
            case $bilcoenddate->format('d'):
                $tahap = 1;
                break;
            default:
                $tahap = 2;
        }
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
      'status' => 'QUEUE',
	'tipe' => 'dataserah',
	'filename' => 'dataserah '.$date->format('Ymd')
    ));
$ndataserah['imported'] = 0;
$ndataserah['stored'] = 0;
    $cx->chunk(100000, function($cx) use($x,$tahap,$importer,$ndataserah) {
    //dd($cx->toArray());
    //$cx->each(function($row) use ($x,$importer) {
$x = array();
	    $importer->importedRow =$ndataserah['imported'] += $cx->count();
	    $importer->save();
	    foreach($cx->toArray() as $row){
            $i =  (array) $row;
	    //$row = $i = $cx->toArray();
	    //dd($row);
            $i['cek_cp'] = false;
            $i['cek_halo'] = false;
            if(strlen(preg_replace('/\D/',null,ltrim($row->customer_phone,0 ))) >= 6){
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

            $i['kpi'] = '';
            if($row->bucket_2 > 0 AND $row->bucket_1 > 0){
              $i['kpi'] = '30-60';
              if($tahap === 1){
                  $i['kpi'] = '60-90';
              }
            }
            if($row->bucket_3 > 0 && $row->bucket_2 > 0){
              $i['kpi'] = '60-90';
              if($tahap === 1){
                  $i['kpi'] = '90-120';
                  if($row->bucket_3 <= 12500 && $tahap === 1){
                      $i['kpi'] = '60-90';
                  }
              }

            }
            if($row->bucket_4 > 0 && $row->bucket_3 > 0){
              $i['kpi'] = '90-120';
            }
            $i['total_outstanding'] = $row->bucket_4 + $row->bucket_3 + $row->bucket_2 + $row->bucket_1;
            $i['import_batch']= $importer->id;

               $x[] = $i;
	    }
        //});
	    $x = collect($x);
	    foreach ($x->chunk(500) as $insert){
	    //dd($insert);
	    $ndataserah['stored'] += count($insert->toArray());
            BilcoDataSerah::insert($insert->toArray());
	    }
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
