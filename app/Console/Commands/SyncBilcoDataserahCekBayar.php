<?php

namespace App\Console\Commands;

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
        $x_endofmonth = Carbon::createFromFormat('Ymd','20201109')->endOfMonth();
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
                $this->info('skip periode :'. $basedate->format('Y-m-d') .':' .$row .'');

                if($currdate >= $startdate){
                    echo ' startfrom :'.$startdate;
                    $updatedate = $row;
                    echo 'update :' . $updatedate;
                    foreach ($xdata as $y){
                        $y = (array) $y;
                        if($y['c30'] != $y['b30']){
                            //BilcodataserahCekBayar::
                            $insert = array(
                                'periode' => $y['tahap_date'],
                                'periode_upd' => $row,
                                'tahap' => $y['tahap_periode'],
                                'account' => $y['account'],
                                'customer_id' => $y['customer_id'],
                                'msisdn' => $y['msisdn'],
                                'hlr_region' => $y['hlr_region'],
                                'nominal_bayar' => $y['b30'] - $y['c30'],
                                'kpi' => 30,
                                'import_batch' => $importer->id
                            );
                            if($y['c30']==0){
                                $insert['detil_pembayaran'] = sprintf('Dibayar penuh pada %s total tagihan dibayar = %s', $row, $y['b30']+$y['c30']);
                            }else{
                                $insert['detil_pembayaran'] = sprintf('Dibayar partial pada %s total tagihan dibayar = %s', $row, $y['b30']+$y['c30']);
                            }
                            SyncBilcoDataserahCekBayarLog::insert($insert);
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b30' => $y['c30'], 'update_date' => $updatedate]);
                        }
                        if($y['c60'] != $y['b60']){
                            $insert = array(
                                'periode' => $y['tahap_date'],
                                'periode_upd' => $row,
                                'tahap' => $y['tahap_periode'],
                                'account' => $y['account'],
                                'customer_id' => $y['customer_id'],
                                'msisdn' => $y['msisdn'],
                                'hlr_region' => $y['hlr_region'],
                                'nominal_bayar' => $y['b60'] - $y['c60'],
                                'kpi' => 60,
                                'import_batch' => $importer->id
                            );
                            if($y['c60']==0){
                                $insert['detil_pembayaran'] = sprintf('Dibayar penuh pada %s total tagihan dibayar = %s', $row, $y['b60']+$y['c60']);
                            }else{
                                $insert['detil_pembayaran'] = sprintf('Dibayar partial pada %s total tagihan dibayar = %s', $row, $y['b60']+$y['c60']);
                            }
                            SyncBilcoDataserahCekBayarLog::insert($insert);
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b60' => $y['c60'], 'update_date' => $updatedate]);
                        }
                        if($y['c90'] != $y['b90']){
                            $insert = array(
                                'periode' => $y['tahap_date'],
                                'periode_upd' => $row,
                                'tahap' => $y['tahap_periode'],
                                'account' => $y['account'],
                                'customer_id' => $y['customer_id'],
                                'msisdn' => $y['msisdn'],
                                'hlr_region' => $y['hlr_region'],
                                'nominal_bayar' => $y['b90'] - $y['c90'],
                                'kpi' => 90,
                                'import_batch' => $importer->id
                            );
                            if($y['c90']==0){
                                $insert['detil_pembayaran'] = sprintf('Dibayar penuh pada %s total tagihan dibayar = %s', $row, $y['b90']+$y['c90']);
                            }else{
                                $insert['detil_pembayaran'] = sprintf('Dibayar partial pada %s total tagihan dibayar = %s', $row, $y['b90']+$y['c90']);
                            }
                            SyncBilcoDataserahCekBayarLog::insert($insert);
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b90' => $y['c90'], 'update_date' => $updatedate]);
                        }
                        if($y['c120'] != $y['b120']){
                            $insert = array(
                                'periode' => $y['tahap_date'],
                                'periode_upd' => $row,
                                'tahap' => $y['tahap_periode'],
                                'account' => $y['account'],
                                'customer_id' => $y['customer_id'],
                                'msisdn' => $y['msisdn'],
                                'hlr_region' => $y['hlr_region'],
                                'nominal_bayar' => $y['b120'] - $y['c120'],
                                'kpi' => 120,
                                'import_batch' => $importer->id
                            );
                            if($y['c120']==0){
                                $insert['detil_pembayaran'] = sprintf('Dibayar penuh pada %s total tagihan dibayar = %s', $row, $y['b120']+$y['c120']);
                            }else{
                                $insert['detil_pembayaran'] = sprintf('Dibayar partial pada %s total tagihan dibayar = %s', $row, $y['b120']+$y['c120']);
                            }
                            SyncBilcoDataserahCekBayarLog::insert($insert);
                            BilcodataserahCekBayar::where('tahap_date',$y['tahap_date'])
                                ->where('tahap_periode', $tahap)
                                ->where('account', $y['account'])
                                ->update(['b120' => $y['c120'], 'update_date' => $updatedate]);
                        }
                    }
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
        if($this->option('from') != 'null'){
            $from = $this->option('from');
        }
        $update = $this->option('update');
        $controller = new BilcodataserahCekBayarController();
        $datex = $this->argument('date');
        $tahap = (int) $this->argument('tahap');
        $date = Carbon::createFromFormat('Y-m-d',$datex.'-01');
        if($update != true){
            if($tahap == 1){
                $date = $date->addDay(-1);
            }
            if($tahap == 2){
                $date = $date->addDay(7-1);
            }
            if($tahap == 3){
                $date = $date->addDay(13-1);
            }

            $x = $controller->fetch($date,$tahap);
            $importer  = Importer::create(array(
                'importedRow'=>0,
                'storedRow'=>0,
                'status' => 'QUEUE',
                'tipe' => 'dataserah:cekbayar',
                'filename' => 'dataserah:cekbayar '.$date->format('Ymd')
            ));
            foreach ($x->get()->toArray() as $row){
                $row = (array) $row;
                $date = Carbon::createFromFormat('Y-m-d', $row['periode']);
                $bilcoenddate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2)->endOfMonth();
                $bilcodate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2);
                $tahap = 0;
                switch ($bilcodate->format('d')){
                    case $bilcoenddate->format('d'):
                        $tahap = 1;
                        break;
                    default:
                        $tahap = 2;
                }
                //$row['kpi'] = '';
                $row['tahap'] = $tahap;
                /*
                if($row['a60'] > 0 AND $row['a30'] > 0){
                    $row['kpi'] = '30-60';
                    if($tahap === 1){
                        $row['kpi'] = '60-90';
                    }
                }
                if($row['a90'] > 0 && $row['a60'] > 0){
                    $row['kpi'] = '60-90';
                    if($tahap === 1){
                        $row['kpi'] = '90-120';
                        if($row['a90'] <= 12500 && $tahap === 1){
                            $row['kpi'] = '60-90';
                        }
                    }

                }
                if($row['a120'] > 0 && $row['a90'] > 0){
                    $row['kpi'] = '90-120';
                }
                */
                $row['total_outstanding'] = $row['a120'] + $row['a90'] + $row['a60'] + $row['a30'];
                $row['update_date'] = $date;
                $row['import_batch'] = $importer->id;
                BilcodataserahCekBayar::insert($row);
                $importer->importedRow =sizeof($row);
                $importer->status = 'finish';
                $importer->save();
            }
        }else{
            $this->update($date,$tahap,$from);
        }
        return 0;
    }
}
