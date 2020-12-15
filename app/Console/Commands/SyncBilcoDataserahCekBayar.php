<?php

namespace App\Console\Commands;

use App\Helpers\AppHelper;
use App\Http\Controllers\API\BilcodataserahCekBayarController;
use App\Importer;
use App\Models\BilcodataserahCekBayar;
use App\SyncBilcoDataserahCekBayarLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncBilcoDataserahCekBayar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncbilcodataserah:cekbayar {date} {tahap} {--update} {--from=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Bayar Bilco DataSerah';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function update($date,$tahap,$from){
        $importer  = Importer::create(array(
            'importedRow'=>0,
            'storedRow'=>0,
            'status' => 'QUEUE',
            'tipe' => 'dataserah:cekbayar update',
            'filename' => 'dataserah:cekbayar update'
        ));
        $basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
        if($from != null) $basedate = Carbon::createFromFormat('Ymd',$date->format('Ym').$from);
        //$basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
        $x =  Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->format("Ymd");
        $x_endofmonth = Carbon::createFromFormat('Ymd',$date->format('Ymd'))->endOfMonth();
        //$x_endofmonth = Carbon::createFromFormat('Ymd','20201109')->endOfMonth();
        $this->info(sprintf('Job #%d update cekbayar from %s until %s',$importer->id,$x,$x_endofmonth->format('Ymd')));
        $date = [$date,$x_endofmonth];
        $existtable = [];

        while($x <= $date[1]->format('Ymd')){
            $table = sprintf('%s_Sumatra',$x);
            $exist = Schema::connection('mysql2')->hasTable($table);
            echo 'cek : '.$table;
            echo ($exist ? ' exist' : ' not exist');
            echo PHP_EOL;

            if($exist == 1){
                $existtable[] = $x;
            }
            $x++;
        }

        foreach ($existtable as $row){
            //BilcodataserahCekBayar::where('tahap_date',$basedate->format('Ymd'));
            if($tahap === 1){
                $sel = 'a.b30,
                    a.b60,
                    a.b90,
                    a.b120,
                    b.bucket_2 as c30,
                    b.bucket_3 as c60,
                    b.bucket_4 as c90,
                    b.bucket_5 as c120';
            }else{
                $sel = "a.b30,
                    a.b60,
                    a.b90,
                    a.b120,
                    b.bucket_1 as c30,
                    b.bucket_2 as c60,
                    b.bucket_3 as c90,
                    b.bucket_4 as c120";
            }
            if($row){
                $this->info('trying '.$row.'_Sumatra');
                echo PHP_EOL;
                $x= DB::table('sabyan_r7s.bilcodataserah_cek_bayars AS a')
                    ->select(DB::raw(
                        'a.periode,
                        a.tahap_date,
                        a.tahap_periode,
                        a.hlr_region,
                    a.account,
                    a.msisdn,
                    a.customer_id,
                    '.$sel.',
                    a.bill_cycle as bill_cycle,
                    a.hlr_region as hlr_region')
                    )
                    ->Join('sabyan_r7s_data.'.$row.'_Sumatra as b', function($join)
                    {
                        $join->on('a.account','=','b.account_number');
                    })
                    ->where('a.tahap_date',Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->startOfMonth())
                    ->where('a.tahap_periode',$tahap)
                    ->where('a.update_date',Carbon::createFromFormat('Ymd',$row)->addDay(-1)->format('Y-m-d'));


                $xdata = $x->get()->toArray();
                //echo Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->startOfMonth();
                //dd($xdata);
                $startdate = Carbon::createFromFormat('Y-m-d',$xdata[0]->periode)->format('Ymd');
                $currdate = Carbon::createFromFormat('Ymd',explode('_',$row)[0])->format('Ymd');
                $this->warn('periode :'. $basedate->format('Y-m-d') .' process :' .$row .'');

                if($currdate > $startdate){
                    echo ' startfrom :'.$startdate;
                    $updatedate = $row;
                    echo 'update :' . $updatedate;
                    $bar = $this->output->createProgressBar($x->count());
                    $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%");
                    $bar->start();
                    foreach ($xdata as $y){
                        $bar->advance();
                        $y = (array) $y;

                        $insertx = array(
                            'periode' => $y['tahap_date'],
                            'periode_upd' => $row,
                            'tahap' => $y['tahap_periode'],
                            'account' => $y['account'],
                            'customer_id' => $y['customer_id'],
                            'msisdn' => $y['msisdn'],
                            'hlr_region' => $y['hlr_region'],
                            'bill_cycle' => $y['bill_cycle']
                        );
                        $check =  [
                            ['periode', '=', $y['tahap_date']],
                            ['periode_upd', '=',$row],
                            ['tahap', '=',  $y['tahap_periode']],
                            ['account', '=', $y['account']],
                        ];
                        $record = SyncBilcoDataserahCekBayarLog::where($check);
                        $update = [];
                        if($y['c30'] != $y['b30']){
                            $update = null;
                            $update = ['h30' =>  $y['b30'] - $y['c30'],'h30f' =>  $y['c30']];
                            if($y['c30']==0){
                                $update['full_30'] = 1;
                            }else{
                                $update['full_30'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b30' => $y['c30'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c60'] != $y['b60']){
                            $update = null;
                            $update = ['h60' =>  $y['b60'] - $y['c60'],'h60f' =>  $y['c60']];
                            if($y['c60']==0){
                                $update['full_60'] = 1;
                            }else{
                                $update['full_60'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b60' => $y['c60'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c90'] != $y['b90']){
                            $update = null;
                            $update = ['h90' =>  $y['b90'] - $y['c90'],'h90f' =>  $y['c90']];
                            if($y['c90']==0){
                                $update['full_90'] = 1;
                            }else{
                                $update['full_90'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b90' => $y['c90'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c120'] != $y['b120']){
                            $update = null;
                            $update = ['h120' =>  $y['b120'] - $y['c120'],'h120f' =>  $y['c120']];
                            if($y['c120']==0){
                                $update['full_120'] = 1;
                            }else{
                                $update['full_120'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b120' => $y['c120'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        //$updold = $update;
                        $update = [
                            'h120' =>  $y['b120'] - $y['c120'],
                            'h120f' =>  $y['c120'],
                            'h90' =>  $y['b90'] - $y['c90'],
                            'h90f' =>  $y['c90'],
                            'h60' =>  $y['b60'] - $y['c60'],
                            'h60f' =>  $y['c60'],
                            'h30' =>  $y['b30'] - $y['c30'],
                            'h30f' =>  $y['c30']
                        ];

                        if($record->exists()){
                            $record->update($update);

                        }else{
                            SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                        }
                    }

                    $bar->finish();
                }
                $importer->status = 'finish';
                $importer->save();
            }

        }
    }
    public function updatex($date,$tahap,$from){
        $importer  = Importer::create(array(
            'importedRow'=>0,
            'storedRow'=>0,
            'status' => 'QUEUE',
            'tipe' => 'dataserah:cekbayar update',
            'filename' => 'dataserah:cekbayar update'
        ));
        $basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
        if($from != null) $basedate = Carbon::createFromFormat('Ymd',$date->format('Ym').$from);
        //$basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
        $x =  Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->format("Ymd");
        $x_endofmonth = Carbon::createFromFormat('Ymd',$date->format('Ymd'))->endOfMonth();
        //$x_endofmonth = Carbon::createFromFormat('Ymd','20201109')->endOfMonth();
        $this->info(sprintf('Job #%d update cekbayar from %s until %s',$importer->id,$x,$x_endofmonth->format('Ymd')));
        $date = [$date,$x_endofmonth];
        $existtable = [];

        while($x <= $date[1]->format('Ymd')){
            $table = sprintf('%s_Sumatra',$x);
            $exist = Schema::connection('mysql2')->hasTable($table);
            echo 'cek : '.$table;
            echo ($exist ? ' exist' : ' not exist');
            echo PHP_EOL;

            if($exist == 1){
                $existtable[] = $x;
            }
            $x++;
        }

        foreach ($existtable as $row){
            //BilcodataserahCekBayar::where('tahap_date',$basedate->format('Ymd'));
            if($tahap === 1){
                $sel = 'a.b30,
                    a.b60,
                    a.b90,
                    a.b120,
                    b.bucket_2 as c30,
                    b.bucket_3 as c60,
                    b.bucket_4 as c90,
                    b.bucket_5 as c120';
            }else{
                $sel = "a.b30,
                    a.b60,
                    a.b90,
                    a.b120,
                    b.bucket_1 as c30,
                    b.bucket_2 as c60,
                    b.bucket_3 as c90,
                    b.bucket_4 as c120";
            }
            if($row){
                $this->info('trying '.$row.'_Sumatra');
                echo PHP_EOL;
                $x= DB::table('sabyan_r7s.bilcodataserah_cek_bayars AS a')
                    ->select(DB::raw(
                        'a.periode,
                        a.tahap_date,
                        a.tahap_periode,
                        a.hlr_region,
                    a.account,
                    a.msisdn,
                    a.customer_id,
                    '.$sel.',
                    a.bill_cycle as bill_cycle,
                    a.hlr_region as hlr_region')
                    )
                    ->Join('sabyan_r7s_data.'.$row.'_Sumatra as b', function($join)
                    {
                        $join->on('a.account','=','b.account_number');
                    })
                    ->where('a.tahap_date',Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->startOfMonth())
                    ->where('a.tahap_periode',$tahap);


                $xdata = $x->get()->toArray();
                //echo Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->startOfMonth();
                //dd($xdata);
                $startdate = Carbon::createFromFormat('Y-m-d',$xdata[0]->periode)->format('Ymd');
                $currdate = Carbon::createFromFormat('Ymd',explode('_',$row)[0])->format('Ymd');
                $this->warn('periode :'. $basedate->format('Y-m-d') .' process :' .$row .'');

                if($currdate > $startdate){
                    echo ' startfrom :'.$startdate;
                    $updatedate = $row;
                    echo 'update :' . $updatedate;
                    $bar = $this->output->createProgressBar($x->count());
                    $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%");
                    $bar->start();
                    foreach ($xdata as $y){
                        $bar->advance();
                        $y = (array) $y;

                        $insertx = array(
                            'periode' => $y['tahap_date'],
                            'periode_upd' => $row,
                            'tahap' => $y['tahap_periode'],
                            'account' => $y['account'],
                            'customer_id' => $y['customer_id'],
                            'msisdn' => $y['msisdn'],
                            'hlr_region' => $y['hlr_region'],
                            'bill_cycle' => $y['bill_cycle']
                        );
                        $check =  [
                            ['periode', '=', $y['tahap_date']],
                            ['periode_upd', '=',$row],
                            ['tahap', '=',  $y['tahap_periode']],
                            ['account', '=', $y['account']],
                        ];
                        $record = SyncBilcoDataserahCekBayarLog::where($check);
                        $update = [];
                        if($y['c30'] != $y['b30']){
                            $update = null;
                            $update = ['h30' =>  $y['b30'] - $y['c30'],'h30f' =>  $y['c30']];
                            if($y['c30']==0){
                                $update['full_30'] = 1;
                            }else{
                                $update['full_30'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b30' => $y['c30'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c60'] != $y['b60']){
                            $update = null;
                            $update = ['h60' =>  $y['b60'] - $y['c60'],'h60f' =>  $y['c60']];
                            if($y['c60']==0){
                                $update['full_60'] = 1;
                            }else{
                                $update['full_60'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b60' => $y['c60'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c90'] != $y['b90']){
                            $update = null;
                            $update = ['h90' =>  $y['b90'] - $y['c90'],'h90f' =>  $y['c90']];
                            if($y['c90']==0){
                                $update['full_90'] = 1;
                            }else{
                                $update['full_90'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b90' => $y['c90'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        if($y['c120'] != $y['b120']){
                            $update = null;
                            $update = ['h120' =>  $y['b120'] - $y['c120'],'h120f' =>  $y['c120']];
                            if($y['c120']==0){
                                $update['full_120'] = 1;
                            }else{
                                $update['full_120'] = 0;
                            }
                            if($record->exists()){
                                $record->update($update);
                            }else{
                                SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                            }
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b120' => $y['c120'], 'update_date' => $updatedate,'last_update' => $row]);
                        }
                        //$updold = $update;
                        $update = [
                            'h120' =>  $y['b120'] - $y['c120'],
                            'h120f' =>  $y['c120'],
                            'h90' =>  $y['b90'] - $y['c90'],
                            'h90f' =>  $y['c90'],
                            'h60' =>  $y['b60'] - $y['c60'],
                            'h60f' =>  $y['c60'],
                            'h30' =>  $y['b30'] - $y['c30'],
                            'h30f' =>  $y['c30']
                        ];

                        if($record->exists()){
                            $record->update($update);

                        }else{
                            SyncBilcoDataserahCekBayarLog::insert(array_merge($insertx,$update));
                        }
                    }

                    $bar->finish();
                }
                $importer->status = 'finish';
                $importer->save();
            }

        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from  = null;

        $from = $this->option('from');
        $tahap = $this->argument('tahap');
        $tahap_x = explode(',',$tahap);
        $datex = $this->argument('date');
        $update = $this->option('update');
        foreach ($tahap_x as $tahap){
            $this->info('begin tahap '.$tahap);
            if($this->option('from') != 'null'){
                ($update == false) ? exit('--From must specify --update'): false;
                echo $datex.'-'.$from;
                try {$min_range = Carbon::createFromFormat('Y-m-d',$datex.'-'.$from);}
                catch (\Exception $e){exit($this->error('from value is invalid'));}
                /*switch($tahap){
                    case 1; $min_range->addDay(0); break;
                    case 2; $min_range->addDay(0); break;
                    case 3; $min_range->addDay(0); break;
                }*/
                $max_range = Carbon::createFromFormat('Ymd',$min_range->format('Ymd'))->endOfMonth();
                $param = [ 'min_range' => (int) $min_range->format('d'), 'max_range' => (int)$max_range->format('d')+1];

                //dd($param);
                $from = filter_var(
                    (int) $from,FILTER_VALIDATE_INT, array('options' => $param)
                );
                ($from == false) ? exit($this->error('From is not in allowed range :'.$min_range->format('d').'-'.$param['max_range'])):false;

            }
            try {$date = Carbon::createFromFormat('Y-m-d',$datex.'-01');}
            catch (\Exception $e){exit($this->error('date value is invalid'));}

            ($tahap == 1) ? $date->addDay(-1):false;
            ($tahap == 2) ? $date->addDay(7-1):false;
            ($tahap == 3) ? $date->addDay(13-1):false;
            if($update == false){
                $this->processDatacekbayar($date,$tahap);
            }else{
                $updfrom = ($from == "null"  ) ? Carbon::createFromFormat('Ymd',$date->format('Ymd'))->addDay(1):Carbon::createFromFormat('Y-m-d',$datex.'-'.$from);
                $this->processDatacekbayar($date,$tahap,$updfrom);
            }
        }
        return 0;
    }

    public function checkSabyanTable($date){
            $basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
            //$basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
            $x =  Carbon::createFromFormat('Ymd',$basedate->format('Ymd'))->format("Ymd");
            $x_endofmonth = Carbon::createFromFormat('Ymd',$date->format('Ymd'))->endOfMonth();
            $date = [$date,$x_endofmonth];
            $existtable = [];

            while($x <= $date[1]->format('Ymd')){
                $table = sprintf('%s_Sumatra',$x);
                $exist = Schema::connection('mysql2')->hasTable($table);
                echo 'cek : '.$table;
                echo ($exist ? ' exist' : ' not exist');
                echo PHP_EOL;

                if($exist == 1){
                    $existtable[] = $x;
                }
                $x++;
            }
            return $existtable;
        }

    private function processDatacekbayar($date,$tahap,$update_date_from = null,$update_date_to = null){
        $dataserahdate = $date;
        if($update_date_from != null){
            $update_date_from = $this->checkSabyanTable($update_date_from);
        }

        $controller = new BilcodataserahCekBayarController();
        if(is_array($update_date_from))
        {
            $this->info('Proses Multiple');
            foreach ($update_date_from as $xdate){
                $this->info('Proses '. $xdate);
                $x = $controller->fetch($date,$tahap,Carbon::createFromFormat('Ymd',$xdate));
                $this->fetch($x,$date,Carbon::createFromFormat('Ymd',$xdate));
            }
        }else{
            $x = $controller->fetch($date,$tahap);
            $this->fetch($x,$date);
        }

    }

    private function fetch($dataserah,$date,$bdate = null){
        $importer  = Importer::create(array(
            'importedRow'=>0,
            'storedRow'=>0,
            'status' => 'QUEUE',
            'tipe' => 'dataserah:cekbayar',
            'filename' => 'dataserah:cekbayar '.$date->format('Ymd')
        ));
        $this->info('The import id was : ' . $importer->id);
        $bar = $this->output->createProgressBar($dataserah->count());
        $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%");
        $bar->start();
        $insertdata = [];
        foreach ($dataserah->get()->toArray() as $row){
            $row = (array) $row;
            $date = Carbon::createFromFormat('Y-m-d', $row['periode']);

            $bilcodate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2);
            $bilcoenddate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2)->endOfMonth();
            $tahap = 0;
            switch ($bilcodate->format('d')){
                case $bilcoenddate->format('d'):
                    $tahap = 1;
                    break;
                default:
                    $tahap = 2;
            }

            $row['tahap'] = $tahap;
            $row['total_outstanding'] = $row['a120'] + $row['a90'] + $row['a60'] + $row['a30'];
            $row['last_update']  = $row['update_date'] = ($bdate) ? $bdate->format('Y-m-d') :   $date->addDay(1);
            $row['import_batch'] = $importer->id;
            $insertdata[] = $row;
            $importer->importedRow =sizeof($row);
            $importer->save();
            $bar->advance();
        }
        $bar->finish();
        $insertdata = collect($insertdata);
        $chunks = $insertdata->chunk(500);
        $this->info('chunking');
        $bar = $this->output->createProgressBar(sizeof($chunks));
        $bar->start();
        foreach ($chunks as $chunk){
            $bar->advance();
            BilcodataserahCekBayar::insert($chunk->toArray());
        }
        $bar->finish();
        $importer->status = 'finish';
        $importer->save();
    }
}
