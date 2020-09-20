<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\BilcodataserahCekBayarController;
use App\Models\BilcodataserahCekBayar;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncBilcoDataserahCekBayar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcoDataserah:CekBayar';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new BilcodataserahCekBayarController();
        $x = $controller->fetch();
        $date = Carbon::now();
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
        foreach ($x->get()->toArray() as $row){
            $row = (array) $row;

            $row['kpi'] = '';
            if($row->bucket_2 > 0 AND $row->bucket_1 > 0){
                $row['kpi'] = '30-60';
                if($tahap === 1){
                    $row['kpi'] = '60-90';
                }
            }
            if($row->bucket_3 > 0 && $row->bucket_2 > 0){
                $row['kpi'] = '60-90';
                if($tahap === 1){
                    $row['kpi'] = '90-120';
                    if($row->bucket_3 <= 12500 && $tahap === 1){
                        $row['kpi'] = '60-90';
                    }
                }

            }
            if($row->bucket_4 > 0 && $row->bucket_3 > 0){
                $row['kpi'] = '90-120';
            }
            $row['total_outstanding'] = $row->bucket_4 + $row->bucket_3 + $row->bucket_2 + $row->bucket_1;
            $row['import_batch'] = 0;
            BilcodataserahCekBayar::insert($row);
        }
        return 0;
    }
}
