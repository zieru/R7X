<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\BilcodataserahCekBayar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;
use URL;

class BilcodataserahCekBayarController extends Controller
{
    public function fetch($date,$tahap = null){

        DB::enableQueryLog();
        $date = $date->format('Ymd');
        $adate = Carbon::createFromFormat('Ymd', $date);
        $bdate = Carbon::createFromFormat('Ymd', $date)->addDay(1);

        $x= DB::table('sabyan_r7s.bilco_data_serahs AS a');


        if($tahap === 1){
            $x->select('a.periode',
                'a.account',
                'a.msisdn',
                'a.customer_id',
                'a.bill_amount_04 as ab120',
                'a.bill_amount_03 as ab90',
                'a.bill_amount_02 as ab60',
                'a.bill_amount_01 as ab30',
                'a.bucket_1 as a30',
                'a.bucket_2 as a60',
                'a.bucket_3 as a90',
                'a.bucket_4 as a120',
                'b.bill_amount_5 as bb120',
                'b.bill_amount_4 as bb90',
                'b.bill_amount_3 as bb60',
                'b.bill_amount_2 as bb30',
                'b.bucket_1 as b0',
                'b.bucket_2 as b30',
                'b.bucket_3 as b60',
                'b.bucket_4 as b90',
                'b.bucket_5 as b120',
                'a.bill_cycle as bill_cycle',
                'a.hlr_region as hlr_region'
            );
        }else{
            $x->select('a.periode',
                'a.account',
                'a.msisdn',
                'a.customer_id',
                'a.bill_amount_04 as ab120',
                'a.bill_amount_03 as ab90',
                'a.bill_amount_02 as ab60',
                'a.bill_amount_01 as ab30',
                'a.bucket_1 as a30',
                'a.bucket_2 as a60',
                'a.bucket_3 as a90',
                'a.bucket_4 as a120',
                'b.bill_amount_4 as bb120',
                'b.bill_amount_3 as bb90',
                'b.bill_amount_2 as bb60',
                'b.bill_amount_1 as bb30',
                'b.bucket_1 as b30',
                'b.bucket_2 as b60',
                'b.bucket_3 as b90',
                'b.bucket_4 as b120',
                'a.bill_cycle as bill_cycle',
                'a.hlr_region as hlr_region'
            );
        }
            $x->Join('sabyan_r7s_data.'.$bdate->format('Ymd').'_Sumatra as b', function($join)
            {
                $join->on('a.account','=','b.account_number');
            })
            ->where('a.periode',$adate->format('Y-m-d'));

        //DB::getQueryLog();
        //dd($x->get());
        return $x;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $msisdn = false;
        if($request->has('msisdn') AND strtolower($request->get('msisdn')) != "false" ){
            $msisdn = true;
        }
        DB::enableQueryLog();
        $bill_cycle= $selectbillcycle = $end = $date = null;
        $tahap_d = $request->get('tahap');

        if($request->has('bc') and $request->get('bc') > 0){
            $bill_cycle = $request->get('bc');
        }
        $end = $start = $request->get('periode');



        try {
            $date = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        try {
            $end = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(15);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('End Periode is invalid');}
        $tahap = [];
        if($tahap_d > 0){
            $tahap_dx = 0;
            if($tahap_d == 1){
                $tahap = [$date,Carbon::createFromFormat('Y-m-d', $date->format('Y-m-d'))->addDay(2)] ;
                $tahap_dx = -1;
            }else{
                $tahap = [Carbon::createFromFormat('Y-m-d', $end->format('Y-m-d'))->addDay(-6),Carbon::createFromFormat('Y-m-d', $end->format('Y-m-d'))->addDay(3)] ;
                $tahap_dx = 0;
            }
        }
        //dd($tahap);
        if($request->has('outs') === true){
            $selectbillcycle = 'bill_cycle as bill_cycles,';
        }

        if($request->has('end')){
            $end = null;
            $end = Carbon::createFromFormat('Y-m-d', $request->end.'-01')->format('Y-m-d');
        }
        $d30harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 > 0, 1, 0)) AS ma30,
        SUM(if(a60 > 0, 1, 0)) AS ma60,
        SUM(if(a90 > 0, 1, 0)) AS ma90,
        SUM(if(a120 > 0, 1, 0)) AS ma120,
        SUM(if(b30 > 0, 1, 0)) AS mb30,
        SUM(if(b60 > 0, 1, 0)) AS mb60,
        SUM(if(b90 > 0, 1, 0)) AS mb90,
        SUM(if(b120 > 0, 1, 0)) AS mb120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        "AREA Sumatra" AS regional');
        if($bill_cycle!=null){
            $d30harea->where('bill_cycle',$bill_cycle);
        }
        $d30harea->where('tahap_date',$request->get('periode').'-01');
        if($tahap_d > 0) $d30harea->where('tahap_periode',$tahap_d);
        $d30harea
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC');


        $d30h = BilcodataserahCekBayar::selectRaw('
       sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 > 0, 1, 0)) AS ma30,
        SUM(if(a60 > 0, 1, 0)) AS ma60,
        SUM(if(a90 > 0, 1, 0)) AS ma90,
        SUM(if(a120 > 0, 1, 0)) AS ma120,
        SUM(if(b30 > 0, 1, 0)) AS mb30,
        SUM(if(b60 > 0, 1, 0)) AS mb60,
        SUM(if(b90 > 0, 1, 0)) AS mb90,
        SUM(if(b120 > 0, 1, 0)) AS mb120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
        kpi as periodes,
        kpi as kpis,'.$selectbillcycle.'kpi,
        hlr_region as regional')
            ->groupBy('hlr_region');
        if($bill_cycle!=null){
            $d30h->where('bill_cycle',$bill_cycle);
        }
        $d30h->where('tahap_date',$request->get('periode').'-01');
        if($tahap_d > 0) $d30h->where('tahap_periode',$tahap_d);
        $d30h
            ->orderBy('hlr_region','ASC')
            ->orderBy('kpi','ASC');
        $d30h =$d30h->union($d30harea)->get()->toArray();

        $d90harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 > 0, 1, 0)) AS ma30,
        SUM(if(a60 > 0, 1, 0)) AS ma60,
        SUM(if(a90 > 0, 1, 0)) AS ma90,
        SUM(if(a120 > 0, 1, 0)) AS ma120,
        SUM(if(b30 > 0, 1, 0)) AS mb30,
        SUM(if(b60 > 0, 1, 0)) AS mb60,
        SUM(if(b90 > 0, 1, 0)) AS mb90,
        SUM(if(b120 > 0, 1, 0)) AS mb120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'
            .$selectbillcycle.
            'kpi as kpi,
        "AREA Sumatra" AS regional');
        if($bill_cycle!=null){
            $d90harea->where('bill_cycle',$bill_cycle);
        }
        $d90harea->where('tahap_date',$request->get('periode').'-01');
        if($tahap_d > 0) $d90harea->where('tahap_periode',$tahap_d);

        $d90harea->groupBy('bill_cycle')
            ->orderBy('bill_cycle','ASC')
            ->orderBy('kpi','ASC');
        $d90harea =$d90harea;

        $d90h = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 > 0, 1, 0)) AS ma30,
        SUM(if(a60 > 0, 1, 0)) AS ma60,
        SUM(if(a90 > 0, 1, 0)) AS ma90,
        SUM(if(a120 > 0, 1, 0)) AS ma120,
        SUM(if(b30 > 0, 1, 0)) AS mb30,
        SUM(if(b60 > 0, 1, 0)) AS mb60,
        SUM(if(b90 > 0, 1, 0)) AS mb90,
        SUM(if(b120 > 0, 1, 0)) AS mb120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'.$selectbillcycle.'
        bill_cycle as kpi,
        hlr_region as regional')
            ->groupBy('hlr_region');
        if($bill_cycle!=null){
            $d90h->where('bill_cycle',$bill_cycle);
        }
        if($request->has('outs') === false){
            $d90h->groupBy('kpis');
        }else{
            $d90h->groupBy('bill_cycle');
        }

        $d90h
            ->orderBy('hlr_region','DESC')
            ->orderBy('bill_cycle','ASC')
            ->orderBy('kpi','ASC');
        $d90h->where('tahap_date',$request->get('periode').'-01');
        if($tahap_d > 0) $d90h->where('tahap_periode',$tahap_d);

        $d90h =$d90h->union($d90harea)->get()->toArray();


        if($request->has('end')){
            $d30harea2 = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        count(b30) as d30,
        count(b60) as d60,
        count(b90) as d90,
        count(b120) as d120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        "AREA Sumatra" AS regional');
            if($bill_cycle!=null){
                $d30harea2->where('bill_cycle',$bill_cycle);
            }
            $d30harea2->where('tahap_date',$end);
            if($tahap_d > 0) $d30harea2->where('tahap_periode',$tahap_d);
            $d30harea2
                ->orderBy('hlr_region','DESC')
                ->orderBy('kpi','ASC');


            $d30h2 = BilcodataserahCekBayar::selectRaw('
       sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(b30 > 0, 1, 0)) AS d30,
        SUM(if(b60 > 0, 1, 0)) AS d60,
        SUM(if(b90 > 0, 1, 0)) AS d90,
        SUM(if(b120 > 0, 1, 0)) AS d120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
        kpi as periodes,
        kpi as kpis,'.$selectbillcycle.'kpi,
        hlr_region as regional')
                ->groupBy('hlr_region');
            if($bill_cycle!=null){
                $d30h->where('bill_cycle',$bill_cycle);
            }
            $d30h2->where('tahap_date',$end);
            if($tahap_d > 0) $d30h2->where('tahap_periode',$tahap_d);
            $d30h2
                ->orderBy('hlr_region','ASC')
                ->orderBy('kpi','ASC');
            $d30h2 =$d30h2->union($d30harea)->get()->toArray();

            $d90harea2 = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(b30 > 0, 1, 0)) AS d30,
        SUM(if(b60 > 0, 1, 0)) AS d60,
        SUM(if(b90 > 0, 1, 0)) AS d90,
        SUM(if(b120 > 0, 1, 0)) AS d120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'
                .$selectbillcycle.
                'kpi as kpi,
        "AREA Sumatra" AS regional');
            if($bill_cycle!=null){
                $d90harea2->where('bill_cycle',$bill_cycle);
            }
            $d90harea2->where('tahap_date',$end);
            if($tahap_d > 0) $d90harea2->where('tahap_periode',$tahap_d);

            $d90harea2->groupBy('bill_cycle')
                ->orderBy('bill_cycle','ASC')
                ->orderBy('kpi','ASC');
            $d90harea2 =$d90harea2;

            $d90h2 = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,
        sum(a60) as a60,
        sum(a90) as a90,
        sum(a120) as a120,
        sum(b30) as b30,
        sum(b60) as b60,
        sum(b90) as b90,
        sum(b120) as b120,
        sum(h30) as h30,
        sum(h60) as h60,
        sum(h90) as h90,
        sum(h120) as h120,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(b30 > 0, 1, 0)) AS d30,
        SUM(if(b60 > 0, 1, 0)) AS d60,
        SUM(if(b90 > 0, 1, 0)) AS d90,
        SUM(if(b120 > 0, 1, 0)) AS d120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'.$selectbillcycle.'
        bill_cycle as kpi,
        hlr_region as regional')
                ->groupBy('hlr_region');
            if($bill_cycle!=null){
                $d90h2->where('bill_cycle',$bill_cycle);
            }
            if($request->has('outs') === false){
                $d90h2->groupBy('kpis');
            }else{
                $d90h2->groupBy('bill_cycle');
            }

            $d90h2
                ->orderBy('hlr_region','DESC')
                ->orderBy('bill_cycle','ASC')
                ->orderBy('kpi','ASC');
            $d90h2->where('tahap_date',$end);
            if($tahap_d > 0) $d90h2->where('tahap_periode',$tahap_d);

            $d90h2 =$d90h2->union($d90harea2)->get()->toArray();
        }

        $temp = array();
        $dates = array();
        $period = array();
        foreach($d30h as $val){
            $dates[$val['periodes']] = 0;
        }
        foreach ($dates as $key => $val){
            $period[] = $key;
        }
        sort($period);

        $l = 0;
        $sum2 = $sum = $i  = [];

        $kpis = [30,60,90,120,0];
        if($request->has('end')){
            $kpis = ['MoM',$end,$request->periode.'-01'];
        }
        $kpis = array_reverse($kpis);
        //dd($d30h);
        $bcx = 0;
        foreach ($d30h as $row){
            if($row['totalmsisdn'] != null){
                $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
                $row['raw_total'] = $row['total'];
                $row['total_outstanding'] = $row['total'];
                $sum[$row['regional']]['uncollected'] = $row['total_outstanding'] - ($row['b30'] + $row['b60'] + $row['b90'] + $row['b120']);
                $sum[$row['regional']]['pcollection'] = number_format(($sum[$row['regional']]['uncollected']/$row['total_outstanding'])*100,2);
                //$row['total'] = number_format($row['total']);
                if($request->has('outs') === true){
                    $row['kpi'] = 'All BC';
                }
                if($request->has('outs') === false){
                    $row['kpi'] = 'All KPI';
                }
                $l++;
                //$row['totalmsisdn'] = number_format($row['totalmsisdn']);
                $sum[$row['regional']]['total'] = $row['total'];
                $sum[$row['regional']]['totalmsisdn'] = $row['totalmsisdn'];
                $sum[$row['regional']]['collection'] = $row['total_outstanding'];

                $sum[$row['regional']]['periodes'] = $row['periodes'];
                $sum[$row['regional']]['kpis'] = $row['kpis'];
                $sum[$row['regional']]['kpi'] = $row['kpi'];
                $sum[$row['regional']]['regional'] = $row['regional'];
                $sum[$row['regional']]['id'] = $row['id'];
                foreach ($kpis as $p){
                    switch($p){
                        case 0:
                            $dataserah = $row['total'];
                            $collection = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] - $row['b30'] - $row['b60'] - $row['b90'] - $row['b120'];
                            $total  = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] ;
                            $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'];
                            $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'];
                            break;
                        case 30:
                            $dataserah = $row['a30'];
                            $collection = $row['h30'];
                            $total  = $row['a30'];
                            $nmsisdn = $row['ma30'];
                            $bmsisdn = $row['mb30'];
                            break;
                        case 60:
                            $dataserah = $row['a60'];
                            $collection = $row['h60'];
                            $nmsisdn = $row['ma60'];
                            $bmsisdn = $row['mb60'];
                            $total  = $row['a60'];
                            break;
                        case 90:
                            $dataserah = $row['a90'];
                            $collection = $row['h90'];
                            $total  = $row['a90'];
                            $nmsisdn = $row['ma90'];
                            $bmsisdn = $row['mb90'];
                            break;
                        case 120:
                            $dataserah = $row['a120'];
                            $collection = $row['h120'];
                            $total  = $row['a120'];
                            $nmsisdn = $row['ma120'];
                            $bmsisdn = $row['mb120'];
                            break;
                        default:
                            $dataserah = $row['total'];
                            $collection = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] - $row['b30'] - $row['b60'] - $row['b90'] - $row['b120'];
                            $total  = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] ;
                            $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'];
                            $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'];
                    }

                    $pcollection = 0;
                    if($msisdn === true){
                        $collection = $nmsisdn - $bmsisdn;
                        $dataserah = $nmsisdn;
                    }

                    $uncollected = $dataserah - $collection;
                    if($total > 0){
                        $pcollection = ($collection/$dataserah);
                    }

                    $sum[$row['regional']]['period'][$p]['uncollected'] = number_format($uncollected);
                    $sum[$row['regional']]['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                    $sum[$row['regional']]['period'][$p]['collection'] = number_format($collection);
                    $sum[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($dataserah);
                    $sum[$row['regional']]['period'][$p]['total'] = $dataserah;
                }
                foreach ($d90h as $child){

                    /// var_dump($child);
                    $cekkpi = false;
                    if($child['kpis'] === $row['kpis']){
                        $cekkpi = true;
                    }
                    if($request->has('outs') === true){
                        $cekkpi = true;
                    }else{
                        $cekkpi =true;
                        $child['kpi'] = $child['kpis'];
                    }

                    if( $child['regional'] == $row['regional'] ) {
                        //var_dump($child);
                        unset($child['kpis']);
                        $lc = 0;
                        foreach ($kpis as $p){
                            ;                        $lc = $lc+1;
                            switch($p){
                                case 0:
                                    $dataserah = $child['total'];
                                    $collection = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] - $child['b30'] - $child['b60'] - $child['b90'] - $row['b120'];
                                    $total  = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] ;
                                    $nmsisdn = $child['ma30'] + $child['ma60'] + $child['ma90'] + $child['ma120'];
                                    $bmsisdn = $child['mb30'] + $child['mb60'] + $child['mb90'] + $child['mb120'];
                                    break;
                                case 30:
                                    $dataserah = $child['a30'];
                                    $collection = $child['h30'];
                                    $nmsisdn = $child['ma30'];
                                    $bmsisdn = $child['mb30'];
                                    $total  = $child['a30'];
                                    break;
                                case 60:
                                    $dataserah = $child['a60'];
                                    $collection = $child['h60'];
                                    $nmsisdn =$child['ma60'];
                                    $bmsisdn = $child['mb60'];
                                    $total  = $child['a60'];
                                    break;
                                case 90:
                                    $dataserah = $child['a90'];
                                    $collection = $child['h90'];
                                    $nmsisdn = $child['ma90'];
                                    $bmsisdn = $child['mb90'];
                                    $total  = $child['a90'];
                                    break;
                                case 120:
                                    $dataserah = $child['a120'];
                                    $collection = $child['h120'];
                                    $nmsisdn = $child['ma120'];
                                    $bmsisdn = $child['mb120'];
                                    $total  = $child['a120'];
                                    break;
                                default:
                                    $dataserah = $child['total'];
                                    $collection = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] - $child['b30'] - $child['b60'] - $child['b90'] - $row['b120'];
                                    $total  = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] ;
                                    $nmsisdn = $child['ma30'] + $child['ma60'] + $child['ma90'] + $child['ma120'];
                                    $bmsisdn = $child['mb30'] + $child['mb60'] + $child['mb90'] + $child['mb120'];
                            }
                            if($msisdn === true){
                                $dataserah = $nmsisdn;
                                $collection = $nmsisdn - $bmsisdn;
                                //$sum[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($row['totalmsisdn']);
                            }
                            $uncollected = $dataserah - $collection;
                            $pcollection = 0;
                            if($total > 0){
                                $pcollection = ($collection/$dataserah);
                            }
                            $child['id'] = sprintf('sub#%s#%s#%s#%s#%s',$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                            $child['period'][$p]['total'] = number_format($total);
                            $child['period'][$p]['uncollected'] = number_format($uncollected);
                            $child['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                            $child['period'][$p]['collection'] = number_format($collection);
                            $child['period'][$p]['totalmsisdn'] = number_format($dataserah);
                            $child['period'][$p]['total'] = $dataserah;
                            if(strtolower($child['regional']) == 'area sumatra'){
                                //var_dump($child);
                            }

                        }
                        $row['children'][$row['kpi']][] = $child;
                        $region = $child['regional'];
                        if($request->has('outs') === false){
                            $child['regional'] = '';
                        }else{
                            $child['kpi'] = $child['bill_cycles'];
                        }
                        $del = ['a30','h30','b30','a60','h60','b60','a90','h90','b90','a120','h120','b120','c30','c60','c90','c120','periodes','total','totalmsisdn','120','90','60','30'];
                        foreach($del as $childdel){
                            unset($child[$childdel]);
                        }
                        $sum[$region]['children'][] = $child;

                    }
                }
                unset($row['kpis']);
            }


            $i[$row['regional']]=$row;

            $temp[] = $row ;
            if($bcx === 10)dd($row);
            $bcx +=1;
        }
        if($request->has('end')){
            foreach ($d30h2 as $row){
                if($row['totalmsisdn'] != null){
                    $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
                    $row['raw_total'] = $row['total'];
                    $row['total_outstanding'] = $row['total'];
                    $sum2[$row['regional']]['uncollected'] = $row['total_outstanding'] - ($row['b30'] + $row['b60'] + $row['b90'] + $row['b120']);
                    $sum2[$row['regional']]['pcollection'] = number_format(($sum2[$row['regional']]['uncollected']/$row['total_outstanding'])*100,2);
                    //$row['total'] = number_format($row['total']);
                    if($request->has('outs') === true){
                        $row['kpi'] = 'All BC';
                    }
                    if($request->has('outs') === false){
                        $row['kpi'] = 'All KPI';
                    }
                    $l++;
                    //$row['totalmsisdn'] = number_format($row['totalmsisdn']);
                    $sum2[$row['regional']]['total'] = $row['total'];
                    $sum2[$row['regional']]['totalmsisdn'] = $row['totalmsisdn'];
                    $sum2[$row['regional']]['collection'] = $row['total_outstanding'];

                    $sum2[$row['regional']]['periodes'] = $row['periodes'];
                    $sum2[$row['regional']]['kpis'] = $row['kpis'];
                    $sum2[$row['regional']]['kpi'] = $row['kpi'];
                    $sum2[$row['regional']]['regional'] = $row['regional'];
                    $sum2[$row['regional']]['id'] = $row['id'];
                    foreach ($kpis as $p){
                        switch($p){
                            case 0:
                                $dataserah = $row['total'];
                                $collection = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] - $row['b30'] - $row['b60'] - $row['b90'] - $row['b120'];
                                $total  = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] ;
                                $nmsisdn = $row['d30'] + $row['d60'] + $row['d90'] + $row['d120'];
                                break;
                            case 30:
                                $dataserah = $row['total'];
                                $collection = $row['a30'] - $row['b30'];
                                $total  = $row['a30'];
                                $nmsisdn = $row['d30'];

                                break;
                            case 60:
                                $dataserah = $row['total'];
                                $collection = $row['a60'] - $row['b60'];
                                $nmsisdn = $row['d60'];
                                $total  = $row['a60'];
                                break;
                            case 90:
                                $dataserah = $row['ab90'];
                                $collection = $row['a90'] - $row['b90'];
                                $total  = $row['a90'];
                                $nmsisdn = $row['d90'];
                                break;
                            case 120:
                                $dataserah = $row['total'];
                                $collection = $row['a120'] - $row['b120'];
                                $total  = $row['a120'];
                                $nmsisdn = $row['d120'];
                                break;
                            default:
                                $dataserah = $row['total'];
                                $collection = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] - $row['b30'] - $row['b60'] - $row['b90'] - $row['b120'];
                                $total  = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] ;
                                $nmsisdn = $row['d30'] + $row['d60'] + $row['d90'] + $row['d120'];
                        }


                        $uncollected = $total - $collection;
                        $pcollection = 0;
                        if($msisdn === true){
                            $uncollected = $total - $collection;
                        }
                        if($total > 0){
                            $pcollection = ($collection/$dataserah);
                        }

                        $sum2[$row['regional']]['period'][$p]['uncollected'] = number_format($uncollected);
                        $sum2[$row['regional']]['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                        $sum2[$row['regional']]['period'][$p]['collection'] = number_format($collection);
                        $sum2[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($dataserah);
                        if($msisdn === true){
                            $sum2[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($nmsisdn);
                            //$sum2[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($row['totalmsisdn']);
                        }
                        $sum2[$row['regional']]['period'][$p]['total'] = $row['total'];
                    }
                    foreach ($d90h2 as $child){

                        /// var_dump($child2);
                        $cekkpi = false;
                        if($child['kpis'] === $row['kpis']){
                            $cekkpi = true;
                        }
                        if($request->has('outs') === true){
                            $cekkpi = true;
                        }else{
                            $cekkpi =true;
                            $child['kpi'] = $child['kpis'];
                        }

                        if( $child['regional'] == $row['regional'] ) {
                            //var_dump($child);
                            unset($child['kpis']);
                            $lc = 0;
                            foreach ($kpis as $p){
                                ;                        $lc = $lc+1;
                                switch($p){
                                    case 0:
                                        $dataserah = $child['total'];
                                        $collection = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] - $child['b30'] - $child['b60'] - $child['b90'] - $row['b120'];
                                        $total  = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] ;
                                        $nmsisdn = $child['ma30'] + $child['ma60'] + $child['ma90'] + $child['ma120'];
                                        break;
                                    case 30:
                                        $dataserah = $child['total'];
                                        $collection = $child['a30'] - $child['b30'];
                                        $nmsisdn = $child['ma30'];
                                        $total  = $child['a30'];
                                        break;
                                    case 60:
                                        $dataserah = $child['total'];
                                        $collection = $child['a60'] - $child['b60'];
                                        $nmsisdn =$child['ma60'];
                                        $total  = $child['a60'];
                                        break;
                                    case 90:
                                        $dataserah = $child['total'];
                                        $collection = $child['a90'] - $child['b90'];
                                        $nmsisdn = $child['ma90'];
                                        $total  = $child['a90'];
                                        break;
                                    case 120:
                                        $dataserah = $child['total'];
                                        $collection = $child['a120'] - $child['b120'];
                                        $nmsisdn = $child['ma120'];
                                        $total  = $child['a120'];
                                        break;
                                    default:
                                        $dataserah = $child['total'];
                                        $collection = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] - $child['b30'] - $child['b60'] - $child['b90'] - $row['b120'];
                                        $total  = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] ;
                                        $nmsisdn = $child['ma30'] + $child['ma60'] + $child['ma90'] + $child['ma120'];
                                }
                                $uncollected = $total - $collection;
                                $pcollection = 0;
                                if($total > 0){
                                    $pcollection = ($collection/$dataserah);
                                }
                                $child['id'] = sprintf('sub#%s#%s#%s#%s#%s',$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                                $child['period'][$p]['total'] = number_format($total);
                                $child['period'][$p]['uncollected'] = number_format($uncollected);
                                $child['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                                $child['period'][$p]['collection'] = number_format($collection);
                                $child['period'][$p]['totalmsisdn'] = number_format($dataserah);
                                $child['period'][$p]['total'] = $row['total'];
                                if(strtolower($child['regional']) == 'area sumatra'){
                                    //var_dump($child);
                                }
                                if($msisdn === true){
                                    $child['period'][$p]['totalmsisdn']= number_format($nmsisdn);
                                    //$sum2[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($row['totalmsisdn']);
                                }
                            }
                            $row['children'][$row['kpi']][] = $child;
                            $region = $child['regional'];
                            if($request->has('outs') === false){
                                $child['regional'] = '';
                            }else{
                                $child['kpi'] = $child['bill_cycles'];
                            }
                            $del = ['a30','h30','b30','a60','h60','b60','a90','h90','b90','a120','h120','b120','c30','c60','c90','c120','periodes','total','totalmsisdn','120','90','60','30'];
                            foreach($del as $childdel){
                                unset($child[$childdel]);
                            }
                            $sum2[$region]['children'][] = $child;

                        }
                    }
                    unset($row['kpis']);
                }

                $i[$row['regional']]=$row;

                $temp[] = $row ;
                if($bcx === 10)dd($row);
                $bcx +=1;
            }
        }

        //$finalsum = [];
        $finalsum = $sum;
        if($request->has('end')){
            foreach($sum as $s => $v){
                $finalsum[$s] = $v;
                if(array_key_exists($s,$sum2)){
                    unset($finalsum[$s]['period'][$request->end.'-01']);
                    $finalsum[$s]['period'][$request->end.'-01'] = $sum2[$s]['period'][$end];
                    $finalsum[$s]['period']['MoM'] = array(
                        'total' => number_format($finalsum[$s]['period'][$start.'-01']['total'] - $finalsum[$s]['period'][$request->end.'-01']['total']),
                        'totalmsisdn' => number_format((int) filter_var($finalsum[$s]['period'][$start.'-01']['totalmsisdn'],FILTER_SANITIZE_NUMBER_INT) - filter_var($finalsum[$s]['period'][$request->end.'-01']['totalmsisdn'],FILTER_SANITIZE_NUMBER_INT)) ,
                        'collection' => number_format((int) filter_var($finalsum[$s]['period'][$start.'-01']['collection'],FILTER_SANITIZE_NUMBER_INT) - filter_var($finalsum[$s]['period'][$request->end.'-01']['collection'], FILTER_SANITIZE_NUMBER_INT)),
                        'uncollected' => number_format((int) filter_var($finalsum[$s]['period'][$start.'-01']['uncollected'],FILTER_SANITIZE_NUMBER_INT) - (int) filter_var($finalsum[$s]['period'][$request->end.'-01']['uncollected'],FILTER_SANITIZE_NUMBER_INT)),
                        'pcollection' => ((int) filter_var($finalsum[$s]['period'][$start.'-01']['pcollection'],FILTER_SANITIZE_NUMBER_INT) - (int) filter_var($finalsum[$s]['period'][$request->end.'-01']['pcollection'],FILTER_SANITIZE_NUMBER_INT)) /100 .'%'
                    );
                    foreach($finalsum[$s]['children'] as $sc => $vc){
                        $finalsum[$s]['children'][$sc]['period'][$request->end.'-01'] = $sum2[$s]['children'][$sc]['period'][$end];
                        $finalsum[$s]['children'][$sc]['period']['MoM'] = array(
                            'total' => number_format($finalsum[$s]['children'][$sc]['period'][$start.'-01']['total'] - $finalsum[$s]['children'][$sc]['period'][$request->end.'-01']['total']),
                            'totalmsisdn' => number_format((int) filter_var($finalsum[$s]['children'][$sc]['period'][$start.'-01']['totalmsisdn'],FILTER_SANITIZE_NUMBER_INT) - filter_var($finalsum[$s]['children'][$sc]['period'][$request->end.'-01']['totalmsisdn'],FILTER_SANITIZE_NUMBER_INT)) ,
                            'collection' => number_format((int) filter_var($finalsum[$s]['children'][$sc]['period'][$start.'-01']['collection'],FILTER_SANITIZE_NUMBER_INT) - filter_var($finalsum[$s]['children'][$sc]['period'][$request->end.'-01']['collection'], FILTER_SANITIZE_NUMBER_INT)),
                            'uncollected' => number_format((int) filter_var($finalsum[$s]['children'][$sc]['period'][$start.'-01']['uncollected'],FILTER_SANITIZE_NUMBER_INT) - (int) filter_var($finalsum[$s]['children'][$sc]['period'][$request->end.'-01']['uncollected'],FILTER_SANITIZE_NUMBER_INT)),
                            'pcollection' => ((int) filter_var($finalsum[$s]['children'][$sc]['period'][$start.'-01']['pcollection'],FILTER_SANITIZE_NUMBER_INT) - (int) filter_var($finalsum[$s]['children'][$sc]['period'][$request->end.'-01']['pcollection'],FILTER_SANITIZE_NUMBER_INT)) /100 .'%'
                        );
                    }
                    //$finalsum[$s]['children']['period'][$request->end.'-01'] = $sum2[$s]['children']['period'][$end];
                }
            }
        }

        //dd(DB::getQueryLog());
        return datatables()->of($finalsum)->with('datecolumn',$kpis)->toJson();
    }
    public function mom(Request $request){
        $bill_cycle= $selectbillcycle = $end = $date = null;
        $tahap_d = $request->get('tahap');

        if($request->has('bc') and $request->get('bc') > 0){
            $bill_cycle = $request->get('bc');
        }
        $start = explode(':',$request->get('periode'))[0];
        $end = explode(':',$request->get('periode'))[1];
        try {
            $date = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        try {
            $end = Carbon::createFromFormat('Y-m', $end)->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('End Periode is invalid');}

        $period = CarbonPeriod::create($start, '1 month', $end);
        $tahap = [];
        if($tahap_d > 0){
            $tahap_dx = 0;
            if($tahap_d == 1){
                $tahap_dx = -1;
            }else{
                $tahap_dx = 14;
            }
            foreach ($period as $dt) {
                $tahap[] = $dt->addDay($tahap_dx);
            }
        }
        if($request->has('outs') === true){
            $selectbillcycle = 'bill_cycle as bill_cycles,';
        }

        $d30harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        "AREA Sumatra" AS regional')
            ->groupBy('periodes');
        if($bill_cycle!=null){
            $d30harea->where('bill_cycle',$bill_cycle);
        }
        if(sizeof($tahap)>0){
            $d30harea->where(function($query)use($tahap) {
                foreach ($tahap as $thpd){
                    $query->orwhere('periode',$thpd->format('Y-m-d'));
                }
            });
        }
        if($request->has('outs') == false){
            //$d30harea->groupBy('kpi');
        }
        $d30harea->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC');

        $d30harea = $d30harea;
        $d30h = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        hlr_region as regional')
            ->groupBy('periodes','hlr_region');
        if($request->has('outs') === false){
            //$d30h->groupBy('kpi');
        }
        if($bill_cycle!=null){
            $d30h->where('bill_cycle',$bill_cycle);
        }
        $d30h->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('hlr_region','ASC')
            ->orderBy('kpi','ASC');
        if(sizeof($tahap)>0){
            $d30h->where(function($query)use($tahap) {
                foreach ($tahap as $thpd){
                    $query->orwhere('periode',$thpd->format('Y-m-d'));
                }
            });
        }
        $d30h =$d30h->union($d30harea)->get()->toArray();
        $d90harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'
            .$selectbillcycle.
            'bill_cycle as kpi,
        "AREA Sumatra" AS regional')
            ->groupBy('periodes');
        if($bill_cycle!=null){
            $d90harea->where('bill_cycle',$bill_cycle);
        }
        if(sizeof($tahap)>0){
            $d90harea->where(function($query)use($tahap) {
                foreach ($tahap as $thpd){
                    $query->orwhere('periode',$thpd->format('Y-m-d'));
                }
            });
        }
        if($request->has('outs') === false){
            $d90harea->groupBy('kpi');
        }else{
            $d90harea->groupBy('bill_cycle');
        }
        $d90harea //->groupBy('bill_cycle')
        ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC')
            ->orderBy('bill_cycle','ASC');
        $d90harea =$d90harea;
        $helper['selectbillcycle'] = $selectbillcycle;
        $d90h2 = BilcoDataSerah::selectRaw('*')
            ->fromSub(function ($query) use($request,$helper) {
                $query->selectRaw('sum(total_outstanding) as total,
                         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
                         count(msisdn) as msidsn1,
                         '.$helper['selectbillcycle'].'
                        bill_cycle as kpi,
                        hlr_region as regional')->from('bilco_data_serahs');
                if($request->has('outs') === false){
                    $query->groupBy('kpis');
                }else{
                    $query->groupBy('bill_cycle');
                }
                $query->groupBy('periodes','hlr_region');
            },'sub')
        ;

        $d90h = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'.$selectbillcycle.'
        bill_cycle as kpi,
        hlr_region as regional')
            ->groupBy('periodes','hlr_region');
        if($bill_cycle!=null){

            $d90h->where('bill_cycle',$bill_cycle);
        }
        if($request->has('outs') === false){
            $d90h->groupBy('kpis');
        }else{
            $d90h->groupBy('bill_cycle');
        }

        $d90h->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC')
            ->orderBy('bill_cycle','ASC');
        //dd($d90h->get()->toArray());
        if(sizeof($tahap)>0){
            $d90h->where(function($query)use($tahap) {
                foreach ($tahap as $thpd){
                    $query->orwhere('periode',$thpd->format('Y-m-d'));
                }
            });
        }
        //dd($d90h->union($d90harea)->get()->toArray());
        $d90h =$d90h->union($d90harea)->get()->toArray();
        //dd($d90h);
        $temp = array();
        $dates = array();
        $period = array();
        $newc = [];
        //dd($d90h);
        foreach ($d90h as $d90h_newc){
            if($request->get('outs') === 'true') {
                $newc[$d90h_newc['regional']][$d90h_newc['bill_cycles']][$d90h_newc['periodes']] = $d90h_newc;
            }else{
                $newc[$d90h_newc['regional']][$d90h_newc['kpis']][$d90h_newc['periodes']] = $d90h_newc;
            }
        }

        //dd($newc);
        foreach($d30h as $val){
            $dates[$val['periodes']] = 0;
        }
        foreach ($dates as $key => $val){
            $period[] = $key;
        }
        sort($period);

        //$kpis = [30,60,90,120,0];
        $kpis = [0];
        $l = 0;
        $sum = $i  = [];

        foreach ($d30h as $row){
            $ncperiod = [];
            $row['total'] = number_format($row['total']);
            $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
            $param = [];
            if($request->has('outs') === true or $request->get('outs') === 'true'){
                $row['kpi'] = 'All BC';
                $param = array(1,6,11,16,20);
            }
            if($request->has('outs') === false OR $request->get('outs') === 'false'){
                $row['kpi'] = 'All KPI';
                $param = array('30-60','60-90','90-120','120-140');
            }
            $l++;
            $row['totalmsisdn'] = number_format($row['totalmsisdn']);
            $sum[$row['regional']]['total'] = $row['total'];
            $sum[$row['regional']]['totalmsisdn'] = $row['totalmsisdn'];
            $sum[$row['regional']]['periodes'] = $row['periodes'];
            $sum[$row['regional']]['kpis'] = $row['kpis'];
            $sum[$row['regional']]['kpi'] = $row['kpi'];
            $sum[$row['regional']]['regional'] = $row['regional'];
            $sum[$row['regional']]['id'] = $row['id'];

            //dd($newc);
            //dd($newc['Sumbagsel'][1]['08-2020']['total']);
            foreach ($period as $p){
                if($p === (string) $row['periodes']){
                    $dataserah = $row['ab30'] + $row['ab60'] + $row['ab90'] + $row['ab120'];
                    $collection = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] - $row['b30'] - $row['b60'] - $row['b90'] - $row['b120'];
                    $total  = $row['a30'] + $row['a60'] + $row['a90'] + $row['a120'] ;

                        $uncollected = $total - $collection;
                        $pcollection = 0;
                        if($total > 0){
                            $pcollection = ($collection/$dataserah);
                        }
                        $sum[$row['regional']]['period'][$p]['uncollected'] = number_format($uncollected);
                        $sum[$row['regional']]['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                        $sum[$row['regional']]['period'][$p]['collection'] = number_format($collection);

                        $sum[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($dataserah);
                        $sum[$row['regional']]['period'][$p]['total'] = $row['total'];
                    }
                    $row['x'][$p]['total'] = $row['total'];
                    $row['x'][$p]['totalmsisdn'] = $row['totalmsisdn'];
                    $loop = 0;
                    foreach ($param as $bc){
                        //dd($newc);
                        //var_dump($row);
                        //echo $newc[$row['AREA Sumatra']][$bc][$p]['kpi'];
                        //          dd(sprintf('$newc[%s][%s][%s][total]',$row['regional'],$bc,$p));
                        //echo sprintf('$newc[%s][%s][%s][total]',$row['regional'],$bc,$p);
                        if(isset($newc[$row['regional']][$bc])){
                            if($request->has('outs') === false OR $request->get('outs') === 'false'){
                                $ncperiod[$loop]= array(
                                    'kpi' => $bc,
                                    'id' => sprintf('sub%s/%s#%s#%s#%s',$l,$loop,$row['regional'],$row['periodes'],$row['kpi']),
                                    'regional' => ''
                                );
                            }else{
                                $ncperiod[$loop]= array(
                                    'kpi' => $bc,
                                    'id' => sprintf('sub%s/%s#%s#%s#%s',$l,$loop,$row['regional'],$row['periodes'],$row['kpi']),
                                    'regional' => $row['regional']
                                );
                            }

                            foreach ($period as $px) {
                                if(isset($newc[$row['regional']][$bc][$px])){
                                    $dataserah = $child['ab30'] + $child['ab60'] + $child['ab90'] + $child['ab120'];
                                    $collection = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] - $child['b30'] - $child['b60'] - $child['b90'] - $row['b120'];
                                    $total  = $child['a30'] + $child['a60'] + $child['a90'] + $child['a120'] ;
                                    $uncollected = $total - $collection;
                                    $pcollection = 0;
                                    if($total > 0){
                                        $pcollection = ($collection/$dataserah);
                                    }
                                    $child['period'][$p]['total'] = number_format($total);
                                    $child['period'][$p]['uncollected'] = number_format($uncollected);
                                    $child['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                                    $child['period'][$p]['collection'] = number_format($collection);
                                    $child['period'][$p]['totalmsisdn'] = number_format($dataserah);
                                    $child['period'][$p]['total'] = $row['total'];
                                    $ncperiod[$loop]['period'][$px] = array(
                                        'total' => number_format($newc[$row['regional']][$bc][$px]['total']),
                                        'uncollected' => null,
                                        'totalmsisdn' => number_format($newc[$row['regional']][$bc][$px]['totalmsisdn']));
                                }
                            }
                            $loop++;
                        }

                    }
                    $sum[$row['regional']]['period'][$p]['totalmsisdn'] = $row['totalmsisdn'];
                    $sum[$row['regional']]['period'][$p]['total'] = $row['total'];
            }
            //dd($ncperiod);
            $ncx = [];
            //dd($newc);

            $sum[$row['regional']]['children'] = $ncperiod;
            //dd($sum);
            /*foreach ($d90h as $child){
               /// var_dump($child);
                $cekkpi = false;
                if($child['kpis'] === $row['kpis']){
                    $cekkpi = true;
                }
                if($request->has('outs') === true){
                    $cekkpi = true;
                }else{
                    $cekkpi =true;
                    $child['kpi'] = $child['kpis'];
                }
                if($child['periodes'] === $row['periodes'] && $cekkpi === true && $child['regional'] == $row['regional'] ) {
                    //var_dump($child);
                    unset($child['kpis']);
                    $lc = 0;
                    foreach ($period as $p){
                        $lc = $lc+1;
                        $child['totalmsisdn'] = $child['totalmsisdn'];
                        $child['total'] = $child['total'];
                        if($p === (string) $child['periodes']){
                            $child['id'] = sprintf('sub#%s#%s#%s#%s#%s',$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                            $child['idchild'] = sprintf('sub#%s#%s#%s#%s#%s',$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                            $child[$p]['total'] = $child['total'];
                            $child[$p]['totalmsisdn'] = $child['totalmsisdn'];
                            $child['period'][$p]['total'] = number_format($child['total']);
                            $child['period'][$p]['totalmsisdn'] = number_format($child['totalmsisdn']);
                        }
                    }
                    $row['children'][$row['kpi']][] = $child;
                    $region = $child['regional'];
                    if($request->has('outs') === false){
                        $child['regional'] = '';
                    }else{
                        $child['kpi'] = $child['bill_cycles'];
                    }
                    //sdd($child);
                    $sum[$region]['children'][] = $child;
                }
            }*/
            unset($row['kpis']);
            $i[$row['regional']]=$row;
            $temp[] = $row ;
        }

        return datatables()->of($sum)->with('datecolumn',$period)->toJson();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BilcodataserahCekBayar  $bilcodataserahCekBayar
     * @return \Illuminate\Http\Response
     */
    public function show(BilcodataserahCekBayar $bilcodataserahCekBayar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BilcodataserahCekBayar  $bilcodataserahCekBayar
     * @return \Illuminate\Http\Response
     */
    public function edit(BilcodataserahCekBayar $bilcodataserahCekBayar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BilcodataserahCekBayar  $bilcodataserahCekBayar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BilcodataserahCekBayar $bilcodataserahCekBayar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BilcodataserahCekBayar  $bilcodataserahCekBayar
     * @return \Illuminate\Http\Response
     */
    public function destroy(BilcodataserahCekBayar $bilcodataserahCekBayar)
    {
        //
    }
}
