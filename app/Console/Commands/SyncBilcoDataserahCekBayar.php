<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\BilcodataserahCekBayarController;
use App\Models\BilcodataserahCekBayar;
use App\Models\SyncBilcoDataserahCekBayarLog;
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
    protected $signature = 'SyncBilcoDataserah:CekBayar {date} {tahap} {--update}';

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

    public function update($date,$tahap){
        $basedate = Carbon::createFromFormat('Ymd',$date->format('Ymd'));
        $x =  $date->format('Ymd');
        $date = [$date,$date->endOfMonth()];
        $existtable = [];
        while($x <= $date[1]->format('Ymd')){
            $table = sprintf('%s_Sumatra',$x);
            $exist = Schema::connection('mysql2')->hasTable($table);
            echo 'cek :'.$table;
            echo $exist . PHP_EOL;
            if($exist == 1){
                $existtable[] = $x;
            }
            $x++;
        }

        foreach ($existtable as $row){
            //BilcodataserahCekBayar::where('tahap_date',$basedate->format('Ymd'));
            if($row != $basedate->format('Ymd')){
                $x= DB::table('sabyan_r7s.bilcodataserah_cek_bayars AS a')
                    ->select(DB::raw(
                        'a.tahap_date,
                        a.tahap_periode,
                        a.hlr_region,
                    a.account,
                    a.msisdn,
                    a.customer_id,
                    a.b30,
                    a.b60,
                    a.b90,
                    a.b120,
                    b.bucket_2 as c30,
                    b.bucket_3 as c60,
                    b.bucket_4 as c90,
                    b.bucket_5 as c120,
                    a.bill_cycle as bill_cycle,
                    a.hlr_region as hlr_region')
                    )
                    ->Join('sabyan_r7s_data.'.$row.'_Sumatra as b', function($join)
                    {
                        $join->on('a.account','=','b.account_number');
                    })
                    ->where('a.tahap_date',$basedate->format('Y-m-d'))
                    ->where('a.tahap_periode',$tahap);

                echo $basedate->format('Y-m-d');
                foreach ($x->get()->toArray() as $y){
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
                            'import_batch' => 0
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
                            ->update(['b30' => $y['c30']]);
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
                            'import_batch' => 0
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
                            ->update(['b60' => $y['c60']]);
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
                            'import_batch' => 0
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
                            ->update(['b90' => $y['c90']]);
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
                            'import_batch' => 0
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
                            ->update(['b120' => $y['c120']]);
                    }
                }
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
        $update = $this->option('update');
        $controller = new BilcodataserahCekBayarController();
        $datex = $this->argument('date');
        $tahap = (int) $this->argument('tahap');
        $date = Carbon::createFromFormat('Y-m-d',$datex.'-01');
        if($update != true){
            if($tahap == 1){
                $date = $date->addDay(-1);
            }
            $x = $controller->fetch($date);
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
                $row['kpi'] = '';
                $row['tahap'] = $tahap;
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
                $row['total_outstanding'] = $row['a120'] + $row['a90'] + $row['a60'] + $row['a30'];
                $row['import_batch'] = 0;
                BilcodataserahCekBayar::insert($row);
            }
        }else{
            $this->update($date,$tahap);
        }
        return 0;
    }
}