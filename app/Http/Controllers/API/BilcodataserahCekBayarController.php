<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\BilcodataserahCekBayar;
use App\SyncBilcoDataserahCekBayarLog;
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
                'a.kpi',
                'a.bill_amount_04 as ab120',
                'a.bill_amount_03 as ab90',
                'a.bill_amount_02 as ab60',
                'a.bill_amount_01 as ab30',
                DB::raw('"0" as a30'),
                'a.bucket_1 as a60',
                'a.bucket_2 as a90',
                'a.bucket_3 as a120',
                'b.bill_amount_5 as bb120',
                'b.bill_amount_4 as bb90',
                'b.bill_amount_3 as bb60',
                'b.bill_amount_2 as bb30',
                //'b.bucket_1 as b0',
                'b.bucket_1 as b30',
                'b.bucket_2 as b60',
                'b.bucket_3 as b90',
                'b.bucket_4 as b120',
                'a.bill_cycle as bill_cycle',
                'a.hlr_region as hlr_region'
            );
        }else{
            $x->select('a.periode',
                'a.account',
                'a.msisdn',
                'a.kpi',
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
        $msisdn = ($request->has('msisdn') AND strtolower($request->get('msisdn')) != "false" ) ? true : false;
        DB::enableQueryLog();

        $msisdnparam  = ($request->has('msisdn') AND  $request->msisdn == 'true') ? true : false;
        $momparam  = ($request->has('end') AND  $request->end != false) ? true : false;
        $selectbillcycle = $end = $date = null;
        $tahap_d = $request->get('tahap');
        $bill_cycle = ($request->has('bc') and $request->get('bc') > 0) ? $bill_cycle = $request->get('bc') : null;
        $end = $start = $request->get('periode');

        try {
            if($request->has('end')){
                $startx = Carbon::createFromFormat('Y-m-d', $start);
                $startx->startOfMonth();
                $end = Carbon::createFromFormat('Y-m-d', $start)->addDay(15);
            }else{
                $startx = Carbon::createFromFormat('Y-m-d', $start.'-01');
                $end = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(15);
            }
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        try {
            if($request->has('end')){
                $startx = Carbon::createFromFormat('Y-m-d', $start);
                $startx->startOfMonth();
                $endx = Carbon::createFromFormat('Y-m-d', $request->end);
                $endx->startOfMonth();
            }else{
                $startx = Carbon::createFromFormat('Y-m-d', $start.'-01');
                $end = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(15);
            }
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('End Periode is invalid');}
        $tahap = [];
        if($tahap_d > 0){
            $tahap_dx = 0;
            if($tahap_d == 1){
                //$tahap = [$date,Carbon::createFromFormat('Y-m-d', $start->format('Y-m-d'))->addDay(2)] ;
                $tahap_dx = -1;
            }else{
                //$tahap = [Carbon::createFromFormat('Y-m-d', $end->format('Y-m-d'))->addDay(-6),Carbon::createFromFormat('Y-m-d', $end->format('Y-m-d'))->addDay(3)] ;
                $tahap_dx = 0;
            }
        }

        $end = ($request->has('end')) ? Carbon::createFromFormat('Y-m-d', $request->end)->format('Y-m-d') : $end;
        $generalcolumn = '
        SUM(IF(tahap_periode != 1, a30, 0)) as a30,
        SUM(IF(tahap_periode != 1, a60, 0)) as a60,
        SUM(IF(tahap_periode != 1, a90, 0)) as a90,
        SUM(IF(tahap_periode != 1, a120, 0)) as a120,
        0 as a30s,
        SUM(IF(tahap_periode = 1, a30, 0)) as a60s,
        SUM(IF(tahap_periode = 1, a60, 0)) as a90s,
        SUM(IF(tahap_periode = 1, a90, 0)) as a120s,
        SUM(IF(tahap_periode != 1, b30, 0)) as b30,
        SUM(IF(tahap_periode != 1, b60, 0)) as b60,
        SUM(IF(tahap_periode != 1, b90, 0)) as b90,
        SUM(IF(tahap_periode != 1, b120, 0)) as b120,
        0 as b30s,
        SUM(IF(tahap_periode = 1, b30, 0)) as b60s,
        SUM(IF(tahap_periode = 1, b60, 0)) as b90s,
        SUM(IF(tahap_periode = 1, b90, 0)) as b120s,
        SUM(IF(tahap_periode != 1, h30, 0)) as h30,
        SUM(IF(tahap_periode != 1, h60, 0)) as h60,
        SUM(IF(tahap_periode != 1, h90, 0)) as h90,
        SUM(IF(tahap_periode != 1, h120, 0)) as h120,
        0 as h30s,
        SUM(IF(tahap_periode = 1, h30, 0)) as h60s,
        SUM(IF(tahap_periode = 1, h60, 0)) as h90s,
        SUM(IF(tahap_periode = 1, h90, 0)) as h120s,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 >0 AND h30 > 0 AND tahap_periode > 1, 1, 0)) AS ma30,
        SUM(if(a60 >0 AND h60 > 0 AND tahap_periode > 1, 1, 0)) AS ma60,
        SUM(if(a90 >0 AND h90 > 0 AND tahap_periode > 1, 1, 0)) AS ma90,
        SUM(if(a120 >0 AND h120 > 0 AND tahap_periode > 1, 1, 0)) AS ma120,
        SUM(if(a30 > 0 AND tahap_periode > 1, 1, 0)) AS mb30,
        SUM(if(a60 > 0 AND tahap_periode > 1, 1, 0)) AS mb60,
        SUM(if(a90 > 0 AND tahap_periode > 1, 1, 0)) AS mb90,
        SUM(if(a120 > 0 AND tahap_periode > 1, 1, 0)) AS mb120,
        SUM(if(h30 > 0 AND tahap_periode = 1, 1, 0)) AS ma30s,
        SUM(if(h60 > 0 AND tahap_periode = 1, 1, 0)) AS ma60s,
        SUM(if(h90 > 0 AND tahap_periode = 1, 1, 0)) AS ma90s,
        SUM(if(a120 > 0 AND tahap_periode = 1, 1, 0)) AS ma120s,
        SUM(if(a30 > 0 AND tahap_periode = 1, 1, 0)) AS mb30s,
        SUM(if(a60 > 0 AND tahap_periode = 1, 1, 0)) AS mb60s,
        SUM(if(a90 > 0 AND tahap_periode = 1, 1, 0)) AS mb90s,
        SUM(if(a120 > 0 AND tahap_periode = 1, 1, 0)) AS mb120s,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi_h as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
         tahap_periode,
        ';
        $d30harea = $this->factoryCekBayar($startx,$tahap_d,false,false,$momparam,false,$request->outs);
        $d30h = $this->factoryCekBayar($startx,$tahap_d,true,false,$momparam,false,$request->outs);
        //dd($d30harea->get()->toArray());
        ///union all///
        $d30h =$d30harea->union($d30h)->get()->toArray();

        $d90harea = $this->factoryCekBayar($startx,$tahap_d,false,true,$momparam,false,$request->outs);
        $d90h = $this->factoryCekBayar($startx,$tahap_d,true,true,$momparam,false,$request->outs);
        //unionall
        $d90h =$d90h->union($d90harea)->get()->toArray();
        //////////////////////
        /////end subtitle/////
        //////////////////////

        if($request->has('end')){
            $generalcolumnmom = 'sum( h120f ) as h120f,
                                 sum( h90f )  as h90f,
                                 sum( h60f )  as h60f,
                                 sum( h30f )  as h30f,
                                 bill_cycle,';
            $d30hareamom = SyncBilcoDataserahCekBayarLog::selectRaw($generalcolumnmom.'"AREA Sumatra" AS regional')
                ->where('periode_upd',$start)->groupBy('hlr_region');
            if($tahap_d > 0) $d30hareamom->where('tahap',$tahap_d);
            $d30hareamom->orderBy('hlr_region','DESC');

            $d30hmom = SyncBilcoDataserahCekBayarLog::selectRaw($generalcolumnmom.'hlr_region AS regional')
                ->where('periode_upd',$start)->groupBy('hlr_region');
            if($tahap_d > 0) $d30hmom->where('tahap',$tahap_d);
            $d30hmom->orderBy('hlr_region','DESC');
            $d30hmom = $d30hmom->union($d30hareamom)->get()->toArray();
            if(sizeof($d30hmom) > 0){
                $d30hmomv = [];
                foreach ($d30hmom as $row){
                    $d30hmomv[$row['regional']]['All BC'] = $row;
                }
            }
            $d90hareamom = SyncBilcoDataserahCekBayarLog::selectRaw($generalcolumnmom.'"AREA Sumatra" AS regional')
                ->where('periode_upd',$start)->groupBy('hlr_region')->groupBy('bill_cycle');;
            if($tahap_d > 0) $d90hareamom->where('tahap',$tahap_d);
            $d90hareamom->orderBy('hlr_region','DESC');

            $d90hmom = SyncBilcoDataserahCekBayarLog::selectRaw($generalcolumnmom.'hlr_region AS regional')
                ->where('periode_upd',$start)->groupBy('hlr_region')->groupBy('bill_cycle');
            if($tahap_d > 0) $d90hmom->where('tahap',$tahap_d);
            $d90hmom->orderBy('hlr_region','DESC');
            $d90hmom = $d90hmom->union($d90hareamom)->get()->toArray();
            $d90hmomv = [];
            if(sizeof($d90hmomv) > 0){
                $d90hmomv = [];
                foreach ($d90hmomv as $row){
                    $d90hmomv[$row['regional']][$row['bill_cycle']] = $row;
                }
            }

            $d30harea2 = $this->factoryCekBayar($endx,$tahap_d,false,false,$momparam,false,$request->outs);
            $d30h2 = $this->factoryCekBayar($endx,$tahap_d,true,false,$momparam,false,$request->outs);
            $d30h2 =$d30h2->union($d30harea2)->get()->toArray();

            $d90harea2 = $this->factoryCekBayar($endx,$tahap_d,false,true,$momparam,false,$request->outs);
            $d90h2 = $this->factoryCekBayar($endx,$tahap_d,true,true,$momparam,false,$request->outs);
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

        $sum2 = $sum = $i  = [];
        $kpis = ($request->has('end')) ? [$start,$end,'MoM'] : [0,120,90,60,30];
        $sum = $this->DataCekBayar($d30h,$d90h,$kpis,$momparam,$msisdnparam);

        //$finalsum = [];
        $finalsum = $sum;
        //dd(DB::getQueryLog());

        //dd($finalsum);
        return datatables()->of($finalsum)->with(['datecolumn' => $kpis ,'endparam'=> $request->has('end'), 'startx' => $startx->format('Y-m-d'),'msisdnparam' => $msisdnparam, 'momparam' => $momparam])->toJson();
    }

    protected function factoryCekBayar($date,$tahap,$isregion,$ischild,$ismom,$ishistoric,$isbc = false){;
        $ret = false;
        $selectbillcycle = ($isbc) ? 'bill_cycle as bill_cycles,' : null;
        //$kpiselect = ($ismom) ? "bill_cycle as kpis,":"kpi as kpis,";
        $generalcolumn = '
        SUM(IF(tahap_periode != 1, a30, 0)) as a30,
        SUM(IF(tahap_periode != 1, a60, 0)) as a60,
        SUM(IF(tahap_periode != 1, a90, 0)) as a90,
        SUM(IF(tahap_periode != 1, a120, 0)) as a120,
        0 as a30s,
        SUM(IF(tahap_periode = 1, a30, 0)) as a60s,
        SUM(IF(tahap_periode = 1, a60, 0)) as a90s,
        SUM(IF(tahap_periode = 1, a90, 0)) as a120s,
        SUM(IF(tahap_periode != 1, b30, 0)) as b30,
        SUM(IF(tahap_periode != 1, b60, 0)) as b60,
        SUM(IF(tahap_periode != 1, b90, 0)) as b90,
        SUM(IF(tahap_periode != 1, b120, 0)) as b120,
        0 as b30s,
        SUM(IF(tahap_periode = 1, b30, 0)) as b60s,
        SUM(IF(tahap_periode = 1, b60, 0)) as b90s,
        SUM(IF(tahap_periode = 1, b90, 0)) as b120s,
        SUM(IF(tahap_periode != 1, h30, 0)) as h30,
        SUM(IF(tahap_periode != 1, h60, 0)) as h60,
        SUM(IF(tahap_periode != 1, h90, 0)) as h90,
        SUM(IF(tahap_periode != 1, h120, 0)) as h120,
        0 as h30s,
        SUM(IF(tahap_periode = 1, h30, 0)) as h60s,
        SUM(IF(tahap_periode = 1, h60, 0)) as h90s,
        SUM(IF(tahap_periode = 1, h90, 0)) as h120s,
        count(a30) as c30,
        count(a60) as c60,
        count(a90) as c90,
        count(a120) as c120,
        SUM(if(a30 >0 AND h30 > 0 AND tahap_periode > 1, 1, 0)) AS ma30,
        SUM(if(a60 >0 AND h60 > 0 AND tahap_periode > 1, 1, 0)) AS ma60,
        SUM(if(a90 >0 AND h90 > 0 AND tahap_periode > 1, 1, 0)) AS ma90,
        SUM(if(a120 >0 AND h120 > 0 AND tahap_periode > 1, 1, 0)) AS ma120,
        SUM(if(a30 > 0 AND tahap_periode > 1, 1, 0)) AS mb30,
        SUM(if(a60 > 0 AND tahap_periode > 1, 1, 0)) AS mb60,
        SUM(if(a90 > 0 AND tahap_periode > 1, 1, 0)) AS mb90,
        SUM(if(a120 > 0 AND tahap_periode > 1, 1, 0)) AS mb120,
        SUM(if(h30 > 0 AND tahap_periode = 1, 1, 0)) AS ma30s,
        SUM(if(h60 > 0 AND tahap_periode = 1, 1, 0)) AS ma60s,
        SUM(if(h90 > 0 AND tahap_periode = 1, 1, 0)) AS ma90s,
        SUM(if(a120 > 0 AND tahap_periode = 1, 1, 0)) AS ma120s,
        SUM(if(a30 > 0 AND tahap_periode = 1, 1, 0)) AS mb30s,
        SUM(if(a60 > 0 AND tahap_periode = 1, 1, 0)) AS mb60s,
        SUM(if(a90 > 0 AND tahap_periode = 1, 1, 0)) AS mb90s,
        SUM(if(a120 > 0 AND tahap_periode = 1, 1, 0)) AS mb120s,
        sum(ab30) as ab30,sum(ab60) as ab60,sum(ab90) as ab90,sum(ab120) as ab120,
        sum(bb30) as bb30,sum(bb60) as bb60,sum(bb90) as bb90,sum(bb120) as bb120,
        sum(total_outstanding) as total,
        sum(total_outstanding_new) as total_new,
        count(msisdn) as totalmsisdn,
         kpi_h as periodes,
         kpi as kpis,
         '.$selectbillcycle.'kpi_h as kpi,
         tahap_periode,
        ';
        //////////////////
        /////maintitle/////
        //////////////////
        ///AREA///
        $ret = ($isregion) ? BilcodataserahCekBayar::selectRaw($generalcolumn.'"AREA Sumatra" AS regional') :
                            BilcodataserahCekBayar::selectRaw($generalcolumn.'hlr_region as regional')
                                ->groupBy('hlr_region');
        $ret->where('tahap_date',$date->format('Y-m-d'));
        ($tahap > 0)  ? $ret->where('tahap_periode',$tahap) : false;
        ($ischild AND $ismom == false) ? $ret->groupBy('bill_cycle')->orderBy('bill_cycle','ASC'): false;
        ($ismom) ? $ret->groupBy('kpi')->orderBy('kpi','ASC'): false;
        $ret->orderBy('hlr_region','DESC');

        return $ret;
    }

    protected function dataCekBayar($d30h,$d90h,$kpis,$mom = false,$msisdn = false){
        $sum = [];
        $l = $bcx = 0;
        foreach ($d30h as $row){
            if($row['totalmsisdn'] != null){
                $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
                $row['raw_total'] = $row['total'];
                $row['total_outstanding'] = $row['total'];
                $sum[$row['regional']]['uncollected'] = $row['total_outstanding'] - ($row['b30'] + $row['b60'] + $row['b90'] + $row['b120']);
                $sum[$row['regional']]['pcollection'] = number_format(($sum[$row['regional']]['uncollected']/$row['total_outstanding'])*100,2);
                //$row['total'] = number_format($row['total']);
                //define label for allbc
                $row['kpi'] = ($mom) ?  'All KPI' : 'All BC';

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

                ///processing main title///
                foreach ($kpis as $p){
                    $sum[$row['regional']]['period'][$p] = $this->countDataCekBayar($p,$row,$msisdn);
                }
                ///end processing main///

                foreach ($d90h as $child){
                    $cekkpi = true;
                    ($mom == false) ? $child['kpi'] = false : $child['kpis'];
                    $child['id'] = sprintf('subchild#%s#%s#%s#%s#%s#%s',0,$l,0,$child['regional'],$child['periodes'],$child['kpi']);
                    if($child['regional'] == $row['regional'] ) {
                        //var_dump($child);
                        unset($child['kpis']);
                        $lc = 0;
                        foreach ($kpis as $p){
                            $lc = $lc+1;
                            $px = $p;
                            $child['period'][$p] = $this->countDataCekBayar($p,$child,$msisdn,true,$mom);
                            $child['period'][$p]['id'] = sprintf('sub#%s#%s#%s#%s#%s#%s',$px,$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                        }
                        $row['children'][$row['kpi']][] = $child;
                        $region = $child['regional'];
                        ($mom != false) ? $child['regional'] = '' : $child['kpi'] = $child['bill_cycles'];
                        $del = [];
                        //$del = ['a30s','a60s','a90s','a120s','ma30','ma60','ma90','ma120','mb30','mb60','mb90','mb120','ab30','ab60','ab90','ab120','bb30','bb60','bb90','bb120','bb30','bb60','bb90','bb120','mb30s','mb60s','mb90s','mb120s','ma120s','ma30s','ma60s','ma90s','ma120s','h120s','h30s','h60s','h90s','h120s','b30s','b60s','b90s','b120s','a30','h30','b30','a60','h60','b60','a90','h90','b90','a120','h120','b120','c30','c60','c90','c120','periodes','total','totalmsisdn','120','90','60','30'];
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
            //if($bcx === 10)dd($row);
            $bcx +=1;
        }

        return $sum;
    }

    protected function countDataCekBayar($p,$row,$ismsisdn = false,$ischild = false, $ismom = false){
        $sum = [];
        $kpihelper = [120,90,60,30];
        //if($ischild AND $ismom) echo $p;
        switch($p) {
            case 0:
                $dataserah = $row['total'];
                $collection = $row['h30'] + $row['h30s'] + $row['h60'] + $row['h60s'] + $row['h90'] + $row['h90s'] + $row['h120'] + $row['h120s'];
                $total = $dataserah;
                $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'] + $row['ma30s'] + $row['ma60s'] + $row['ma90s'] + $row['ma120s'];
                $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'] + $row['mb30s'] + $row['mb60s'] + $row['mb90s'] + $row['mb120s'];

                /*if($d30hmomv) {
                    if ($d30hmomv[$row['regional']][$row['kpi']]) {
                        $collection = $dataserah -
                            (
                                $d30hmomv[$row['regional']][$row['kpi']]['h120f'] +
                                $d30hmomv[$row['regional']][$row['kpi']]['h90f'] +
                                $d30hmomv[$row['regional']][$row['kpi']]['h60f'] +
                                $d30hmomv[$row['regional']][$row['kpi']]['h30f']
                            );
                    }
                }*/
                break;
            case 30:
                $dataserah = $row['a30'] + $row['a30s'];
                $collection = $row['h30'] + $row['h30s'];
                $total = $dataserah;
                $nmsisdn = $row['ma30'] + $row['ma30s'];
                $bmsisdn = $row['mb30'] + $row['mb30s'];
                break;
            case 60:
                $dataserah = $row['a60'] + $row['a60s'];
                $collection = $row['h60'] + $row['h60s'];
                $nmsisdn = $row['ma60'] + $row['ma60s'];
                $bmsisdn = $row['mb60'] + $row['mb60s'];
                $total = $dataserah;
                break;
            case 90:
                $dataserah = $row['a90'] + $row['a90s'];
                $collection = $row['h90'] + $row['h90s'];
                $total = $dataserah;
                $nmsisdn = $row['ma90'] + $row['ma90s'];
                $bmsisdn = $row['mb90'] + $row['mb90s'];
                break;
            case 120:
                $dataserah = $row['a120'] + $row['a120s'];
                $collection = $row['h120'] + $row['h120s'];
                $total = $dataserah;
                $nmsisdn = $row['ma120'] + $row['ma120s'];
                $bmsisdn = $row['mb120'] + $row['mb120s'];
                break;
            default:
                if ($ischild) {
                    foreach ($kpihelper as $kpilabel) {
                        $dataserah[$kpilabel] = $row['a' . $kpilabel] + $row['a' . $kpilabel . 's'];
                        $total[$kpilabel] = $dataserah[$kpilabel];
                        $collection[$kpilabel] = $row['h' . $kpilabel] + $row['h' . $kpilabel . 's'];
                    }
                } else {
                    $dataserah = $row['total'];
                    $total = $dataserah;
                    $collection = $row['h30'] + $row['h30s'] + $row['h60'] + $row['h60s'] + $row['h90'] + $row['h90s'] + $row['h120'] + $row['h120s'];
                }

                $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'] + $row['ma30s'] + $row['ma60s'] + $row['ma90s'] + $row['ma120s'];
                $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'] + $row['mb30s'] + $row['mb60s'] + $row['mb90s'] + $row['mb120s'];
        }

        $pcollection = 0;
        if($ismsisdn === true){
            $collection = $nmsisdn;
            $dataserah = $bmsisdn;
        }

        if(is_array($dataserah)){
            $pcollection = [];
            foreach($kpihelper as $kpilabel){
                $uncollected[$kpilabel] = $dataserah[$kpilabel] - $collection[$kpilabel];
                if($total[$kpilabel] > 0 AND $collection[$kpilabel] > 0){
                    $pcollection[$kpilabel] = ($collection[$kpilabel] / $dataserah[$kpilabel]);
                }
            }
        }else{
            $uncollected = $dataserah - $collection;
            if($total > 0 AND $collection > 0){
                $pcollection = ($collection/$dataserah);
            }
        }

        if(is_array($dataserah)){
            $pcollection = [];
            foreach($kpihelper as $kpilabel){
                if($total[$kpilabel] > 0 AND $collection[$kpilabel] > 0) {
                    $sum['uncollected'][$kpilabel] = number_format($uncollected[$kpilabel]);
                    $sum['pcollection'][$kpilabel] = number_format(($pcollection[$kpilabel]) * 100, 2) . '%';
                    $sum['collection'][$kpilabel] = number_format($collection[$kpilabel]);
                    $sum['totalmsisdn'][$kpilabel] = number_format($dataserah[$kpilabel]);
                    $sum['total'][$kpilabel] = $dataserah[$kpilabel];
                }
            }
        }else{
            $sum['uncollected'] = number_format($uncollected);
            $sum['pcollection'] = number_format(($pcollection)*100,2).'%';
            $sum['collection'] = number_format($collection);
            $sum['totalmsisdn'] = number_format($dataserah);
            $sum['total'] = $dataserah;
        }

        return $sum;
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
         kpi as kpis,'.$selectbillcycle.'kpi_h as kpi,
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
         kpi as kpis,'.$selectbillcycle.'kpi_h as kpi,
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