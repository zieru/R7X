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
    public function fetch($date){

        DB::enableQueryLog();
        $date = $date->format('Ymd');
        $adate = Carbon::createFromFormat('Ymd', $date);
        $bdate = Carbon::createFromFormat('Ymd', $date)->addDay(1);
        $x= DB::table('sabyan_r7s.bilco_data_serahs AS a')
            ->select('a.periode',
                    'a.account',
                    'a.msisdn',
                    'a.customer_id',
                    'a.bill_amount_04 as ab120',
                    'a.bill_amount_03 as ab90',
                    'a.bill_amount_02 as ab60',
                    'a.bill_amount_01 as ab30',
                    'a.bucket_2 as a60',
                    'a.bucket_3 as a90',
                    'a.bucket_4 as a120',
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
            )
            ->Join('sabyan_r7s_data.'.$bdate->format('Ymd').'_Sumatra as b', function($join)
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

        $d30harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        "AREA Sumatra" AS regional');
        if($bill_cycle!=null){
            $d30harea->where('bill_cycle',$bill_cycle);
        }
        if(sizeof($tahap)>0){
            $d30harea->where(function($query)use($tahap) {
                $query->whereBetween('periode',[$tahap[0]->format('Y-m-d'),$tahap[1]->format('Y-m-d')]);
            });
        }else{
            $d30harea->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')));
        }
        if($request->has('outs') == false){
            //$d30harea->groupBy('kpi');
        }
        $d30harea
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC');
        $d30h = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
        kpi as periodes,
        kpi as kpis,'.$selectbillcycle.'kpi,
        hlr_region as regional')
            ->groupBy('hlr_region');
        if($request->has('outs') === false){
            //$d30h->groupBy('kpi');
        }
        if($bill_cycle!=null){
            $d30h->where('bill_cycle',$bill_cycle);
        }
        $d30h
            ->orderBy('hlr_region','ASC')
            ->orderBy('kpi','ASC');
        if(sizeof($tahap)>0){
            $d30h->where(function($query)use($tahap) {
                $query->whereBetween('periode',[$tahap[0]->format('Y-m-d'),$tahap[1]->format('Y-m-d')]);
            });
        }else{
            $d30h->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')));
        }
        $d30h =$d30h->union($d30harea)->get()->toArray();

//        dd(DB::getQueryLog());
        $d90harea = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         kpi as periodes,
         kpi as kpis,'
            .$selectbillcycle.
            'kpi as kpi,
        "AREA Sumatra" AS regional');
        if($bill_cycle!=null){
            $d90harea->where('bill_cycle',$bill_cycle);
        }
        if(sizeof($tahap)>0){
            $d90harea->where(function($query)use($tahap) {
                $query->whereBetween('periode',[$tahap[0]->format('Y-m-d'),$tahap[1]->format('Y-m-d')]);
            });
        }else{
            $d90harea->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')));
        }

        $d90harea->groupBy('bill_cycle')
            ->orderBy('bill_cycle','ASC')
            ->orderBy('kpi','ASC');
        $d90harea =$d90harea;

        //dd($d90harea->get()->toarray());
        $d90h = BilcodataserahCekBayar::selectRaw('
        sum(a30) as a30,sum(a60) as a60,sum(a90) as a90,sum(a120) as a120,sum(b30) as b30,sum(b60) as b60,sum(b90) as b90,sum(b120) as b120,sum(h30) as h30,sum(h60) as h60,sum(h90) as h90,sum(h120) as h120,
        count(a30) as c30,count(a60) as c60,count(a90) as c90,count(a120) as c120,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
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
        //dd($d90h->get()->toArray());
        if(sizeof($tahap)>0){
            $d90h->where(function($query)use($tahap) {
                $query->whereBetween('periode',[$tahap[0]->format('Y-m-d'),$tahap[1]->format('Y-m-d')]);
            });
        }else{
            $d90h->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')));
        }
        $d90h =$d90h->union($d90harea)->get()->toArray();


        //$d30h = $d90h;
        //dd($d30h);
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
        $sum = $i  = [];
        $kpis = [30,60,90,120,0];
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
                            break;
                        case 30:
                            $dataserah = $row['total'];
                            $collection = $row['a30'] - $row['b30'];
                            $total  = $row['a30'];
                            break;
                        case 60:
                            $dataserah = $row['total'];
                            $collection = $row['a60'] - $row['b60'];
                            $total  = $row['a60'];
                            break;
                        case 90:
                            $dataserah = $row['ab90'];
                            $collection = $row['a90'] - $row['b90'];
                            $total  = $row['a90'];
                            break;
                        case 120:
                            $dataserah = $row['total'];
                            $collection = $row['a120'] - $row['b120'];
                            $total  = $row['a120'];
                            break;
                        default:
                            $dataserah = 0;
                            $collection = 0;
                            $total  = 0;
                    }


                    $uncollected = $total - $collection;
                    $pcollection = 0;
                    if($total > 0){
                        $pcollection = ($collection/$dataserah);
                    }

                    $sum[$row['regional']]['period'][$p]['uncollected'] = number_format($uncollected);
                    $sum[$row['regional']]['period'][$p]['pcollection'] = number_format(($pcollection)*100,2).'%';
                    $sum[$row['regional']]['period'][$p]['collection'] = number_format($collection);
                    $sum[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($dataserah);
                    if($msisdn === true){
                        $sum[$row['regional']]['period'][$p]['totalmsisdn'] = number_format($row['totalmsisdn']);
                    }
                    $sum[$row['regional']]['period'][$p]['total'] = $row['total'];
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
                                    break;
                                case 30:
                                    $dataserah = $child['total'];
                                    $collection = $child['a30'] - $child['b30'];
                                    $total  = $child['a30'];
                                    break;
                                case 60:
                                    $dataserah = $child['total'];
                                    $collection = $child['a60'] - $child['b60'];
                                    $total  = $child['a60'];
                                    break;
                                case 90:
                                    $dataserah = $child['total'];
                                    $collection = $child['a90'] - $child['b90'];
                                    $total  = $child['a90'];
                                    break;
                                case 120:
                                    $dataserah = $child['total'];
                                    $collection = $child['a120'] - $child['b120'];
                                    $total  = $child['a120'];
                                    break;
                                default:
                                    $dataserah = 0;
                                    $collection = 0;
                                    $total  = 0;
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

        return datatables()->of($sum)->with('datecolumn',$kpis)->toJson();
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