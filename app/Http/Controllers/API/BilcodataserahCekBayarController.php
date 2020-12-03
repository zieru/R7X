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
    public function fetch($date,$tahap = null,$bdate = null){
        DB::enableQueryLog();
        $isupdate =false;
        $date = $date->format('Ymd');
        $adate = Carbon::createFromFormat('Ymd', $date);

        $bdate = ($bdate == null) ? Carbon::createFromFormat('Ymd', $date)->addDay(1): $bdate;

        $x= DB::table('sabyan_r7s.bilco_data_serahs AS a');


        if($tahap === 1 and $isupdate == false){
            $x->select('a.periode',
                'a.account',
                'a.msisdn',
                'a.customer_id',
                'a.kpi',
                DB::raw('"0" as ab120'),
                'a.bill_amount_03 as ab120',
                'a.bill_amount_02 as ab90',
                'a.bill_amount_01 as ab60',
                DB::raw('"0" as a30'),
                'a.bucket_3 as a120',
                'a.bucket_2 as a90',
                'a.bucket_1 as a60',
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

    public function cekupdate(){
        return datatables()->of($this->factoryCekBayarLastUpdate())->toJson();
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
        $momparam  = ($request->has('end') AND  $request->end != false) ? array(Carbon::createFromFormat('Y-m-d', $request->periode),Carbon::createFromFormat('Y-m-d', $request->end)) : false;
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

        $dateupdate = $this->factoryCekBayarLastUpdate($startx,$tahap_d);
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

            $momparamend = $momparam;
            $momparamend[3] = 'end';
            $d30harea2 = $this->factoryCekBayar($endx,$tahap_d,false,false,$momparamend,false,$request->outs);
            $d30h2 = $this->factoryCekBayar($endx,$tahap_d,true,false,$momparamend,false,$request->outs);
            $d30h2 =$d30h2->union($d30harea2)->get()->toArray();

            $d90harea2 = $this->factoryCekBayar($endx,$tahap_d,false,true,$momparamend,false,$request->outs);
            $d90h2 = $this->factoryCekBayar($endx,$tahap_d,true,true,$momparamend,false,$request->outs);
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
        $sumfinal = [];
        $sum = $this->DataCekBayar($d30h,$d90h,$kpis,$momparam,$msisdnparam);

        $sum2 = ($request->has('end')) ? $this->DataCekBayar($d30h2,$d90h2,$kpis,$momparam,$msisdnparam) : false;

        if($momparam){
            //combining 2 mom
            foreach ($sum as $xi => $x){
                $sumfinal[$x['regional']] = $x;
                $zindex = $yindex = 0;
                foreach ($x['period'] as $yi => $y){
                    if($yindex > 0){
                        if($yindex == 1)$sumfinal[$x['regional']]['period'][$yi] = $this->findmom($yindex,$x,$sum,$sum2,$yi);
                        if($yindex == 2)$sumfinal[$x['regional']]['period']['MoM'] = $this->findmom($yindex,$x,$sum,$sum2,$yi);
                    }
                    $yindex++;
                }
                foreach ($x['children'] as $childid => $period){
                    $zindex=0;
                    foreach ($period['period'] as $zi => $z) {
                        ///0var_dump($zi,$zindex);
                        ///
                        /*echo 'zi :'.$zi.'</br>';
                        echo 'zindex :'.$zindex.'</br>';
                        echo '$childid :'.$childid.'</br>';*/
                        if($zindex == 1){
                            $sumfinal[$x['regional']]['children'][$childid]['period'] = $this->findmomchild($x['regional'],$zindex,$period['period'],$sum,$sum2,$zi,$childid);
                            $sumfinal[$x['regional']]['children'][$childid]['period']['iscal'] = true;
                        }

                        $zindex++;
                    }


                }
            }
        }else{
            $sumfinal = $sum;
        }

        //dd($sumfinal);
        //$finalsum = [];
        $finalsum = $sumfinal;
        //dd(DB::getQueryLog());

        //dd($finalsum);
        return datatables()->of($finalsum)->with(['last_update'=>$dateupdate,'datecolumn' => $kpis ,'endparam'=> $request->has('end'), 'startx' => $startx->format('Y-m-d'),'msisdnparam' => $msisdnparam, 'momparam' => $momparam])->toJson();
    }

    private function findmom($yindex,$x,$sum,$sum2,$yi,$debug =false){
        $ret = false;
        if($yindex == 1){
            if(array_key_exists($x['regional'],$sum2)){
                $ret = $sum2[$x['regional']]['period'][$yi];
            }else{
                $ret = array(
                    'uncollected' => 0,
                    'pcollection' => 0,
                    'collection' => 0,
                    'totalmsisdn' => 0,
                    'total' => 0,
                );
            }
        }
        if($yindex == 2){
            if(array_key_exists($x['regional'],$sum2)) {
                $xmom = array(
                    'uncollected' => number_format($this->helperSanitize($sum2[$x['regional']]['period'][$yi]['uncollected']) - $this->helperSanitize($sum[$x['regional']]['period'][$yi]['uncollected'])),
                    'pcollection' => number_format($this->helperSanitize($sum2[$x['regional']]['period'][$yi]['pcollection']) - $this->helperSanitize($sum[$x['regional']]['period'][$yi]['pcollection'])) . '%',
                    'collection' => number_format($this->helperSanitize($sum2[$x['regional']]['period'][$yi]['collection']) - $this->helperSanitize($sum[$x['regional']]['period'][$yi]['collection'])),
                    'totalmsisdn' => number_format($this->helperSanitize($sum2[$x['regional']]['period'][$yi]['totalmsisdn']) - $this->helperSanitize($sum[$x['regional']]['period'][$yi]['totalmsisdn'])),
                    'total' => number_format($this->helperSanitize($sum2[$x['regional']]['period'][$yi]['total']) - $this->helperSanitize($sum[$x['regional']]['period'][$yi]['total'])),
                );
            }else{
                $xmom = array(
                    'uncollected' => 0,
                    'pcollection' => 0,
                    'collection' => 0,
                    'totalmsisdn' => 0,
                    'total' => 0,
                );
            }
            $ret = $xmom;
        }

        return $ret;
    }
    private function findmomchild($region,$yindex,$x,$sum,$sum2,$yi,$childid,$debug =false){
        $ret = false;
        $sum2 = $sum2[$region]['children'][$childid]['period'][$yi];
        $ret = $x;
        //if($debug) echo 'x';dd($x);

        $rindex =0;
        array(
            'uncollected' => 0,
            'pcollection' => 0,
            'collection' => 0,
            'totalmsisdn' => 0,
            'total' => 0,
        );
        $main = 0;
        foreach ($ret as $ri => $rv){
            if($rindex == 0) {
                $main = $ri;
            }
            if($rindex == 1)$ret[$ri] = $sum2;
            if($rindex == 2 AND $main > 0)
                $ret[$ri] =  array(
                    'uncollected' => number_format($this->helperSanitize($sum2['uncollected']) - $this->helperSanitize($ret[$main]['uncollected'])),
                    'pcollection' => number_format($this->helperSanitize($sum2['pcollection']) - $this->helperSanitize($ret[$main]['pcollection'])) . '%',
                    'collection' => number_format($this->helperSanitize($sum2['collection']) - $this->helperSanitize($ret[$main]['collection'])),
                    'totalmsisdn' => number_format($this->helperSanitize($sum2['totalmsisdn']) - $this->helperSanitize($ret[$main]['totalmsisdn'])),
                    'total' => number_format($this->helperSanitize($sum2['total']) - $this->helperSanitize($ret[$main]['total'])),
                );
            $rindex++;
        }
        return $ret;
    }
    public function export(Request $request){
        var_dump($request->all());
    }

    private function helperSanitize($num){

        return (float) str_replace(',', '', $num);
    }
    /**
     * @param Carbon $date
     * @param int $tahap
     * @return array|bool|null
     */
    protected function factoryCekBayarLastUpdate($date = null, $tahap = 0){
        if($date == null ) $date= Carbon::now()->addDays(-2)->startOfMonth();
        $ret = false;
        if($tahap > 0){
            $x = BilcodataserahCekBayar::select('tahap_periode','update_date')->where('tahap_date',$date->format('Y-m-d'))->orderBy('update_date','DESC')->first();
            $ret = ($x->update_date)? $x->update_date: null;
        }
        else{
            $x = BilcodataserahCekBayar::select('tahap_periode','update_date')->where('tahap_date',$date->format('Y-m-d'))->where('tahap_periode',1)->orderBy('update_date','DESC')->first();
            $ret[] = ($x)? $x->update_date: 'Not started';
            $x = BilcodataserahCekBayar::select('tahap_periode','update_date')->where('tahap_date',$date->format('Y-m-d'))->where('tahap_periode',2)->orderBy('update_date','DESC')->first();
            $ret[] = ($x)? $x->update_date: 'Not started';
            $x = BilcodataserahCekBayar::select('tahap_periode','update_date')->where('tahap_date',$date->format('Y-m-d'))->where('tahap_periode',3)->orderBy('update_date','DESC')->first();
            $ret[] = ($x)? $x->update_date: 'Not started';
        }
        return $ret;
    }
    protected function factoryCekBayar($date,$tahap,$isregion,$ischild,$ismom,$ishistoric,$isbc = false){;
        $ret = false;

        //cek jika mom tidak usah cek lastupdate karna melalui tanggal
        $lastupdate = ($ismom == false) ? $dateupdate = $this->factoryCekBayarLastUpdate($date,$tahap): false;
        $selectbillcycle = ($isbc) ? 'bill_cycle as bill_cycles,' : null;
        //$kpiselect = ($ismom) ? "bill_cycle as kpis,":"kpi as kpis,";
        $generalcolumn = 'SUM(a30) AS a30,
        SUM(a60) AS a60,
        SUM(a90) AS a90,
        SUM(a120) AS a120,
        SUM(b30) AS b30,
        SUM(b60) AS b60,
        SUM(b90) AS b90,
        SUM(b120) AS b120,
        SUM(h30) AS h30,
        SUM(h60) AS h60,
        SUM(h90) AS h90,
        SUM(h120) AS h120,
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
        if($ismom){
            //dd($ismom);
        }


        $ret = ($isregion) ? BilcodataserahCekBayar::selectRaw($generalcolumn.'"AREA Sumatra" AS regional') :
                            BilcodataserahCekBayar::selectRaw($generalcolumn.'hlr_region as regional')
                                ->groupBy('hlr_region');
        $ret->where('tahap_date',$date->format('Y-m-d'));
        if($ismom == false){
        if($lastupdate){
            $ret->where(function($query) use ($lastupdate) {
                if(is_array($lastupdate)){
                    foreach ($lastupdate as $index => $dateupd) {
                        $query->orwhere(function ($query) use ($index, $dateupd) {
                            $query->where('update_date', $dateupd);
                        });
                    }
                }else{
                    $query->where('update_date', $lastupdate);
                }
            });
            }
        }
        if($ismom) {
            if(array_key_exists(3,$ismom)){
                if($ismom[3] == 'start'){
                    $ret->where('update_date',$ismom[0]->format('Y-m-d'));
                }
                elseif($ismom[3] == 'end'){
                    $ret->where('update_date',$ismom[1]->format('Y-m-d'));
                }
            }else{

                $ret->where('update_date',$ismom[0]->format('Y-m-d'));
            }
        };
        ($tahap > 0)  ? $ret->where('tahap_periode',$tahap) : false;
        ($ischild AND $ismom == false) ? $ret->groupBy('bill_cycle')->orderBy('bill_cycle','ASC'): false;
        //($ismom) ? $ret->groupBy('kpi')->orderBy('kpi','ASC'): false;
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

                //dd($kpis);
                ///processing main title///
                foreach ($kpis as $p){
                    $sum[$row['regional']]['period'][$p] = $this->countDataCekBayar($p,$row,$msisdn);
                }
                ///end processing main///
                $childn = 1;
                $d90hx = [];
                foreach ($d90h as $xv){
                    if($mom){
                        $dx= $this->countDataCekBayar($kpis[0],$xv,$msisdn,true,$mom);
                        foreach ($dx as $di => $dv){
                            $chi = $xv;
                            $chi['kpi'] = $di;
                            $d90hx[] = $chi;
                        }
                    }else{
                        $d90hx[] = $xv;
                    }
                }
                ///sddd($d90h);
                foreach ($d90hx as $child){
                    $cekkpi = true;
                    //if($mom) $child['kpi'] = $child['bill_cycles'];
                    if($child['regional'] == $row['regional'] ) {
                        $child['id'] = sprintf('subchild#%s#%s#%s#%s#%s#%s',$childn,$l,0,$child['regional'],$child['periodes'],$child['kpi']);
                        unset($child['kpis']);
                        $lc = 0;


                            foreach ($kpis as $p){
                                $lc = $lc+1;
                                $px = $p;
                                $child['period'][$p]['id'] = sprintf('sub#%s#%s#%s#%s#%s#%s',$px,$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                                if($mom){
                                    $dx= $this->countDataCekBayar($p,$child,$msisdn,true,$mom);
                                    if(array_key_exists($child['kpi'],$dx)) $child['period'][$p] = $dx[$child['kpi']];
                                }else{
                                    $child['period'][$p] = $this->countDataCekBayar($p,$child,$msisdn,true,$mom);
                                }
                            }

                        $row['children'][$row['kpi']][] = $child;
                        $region = $child['regional'];
                        ($mom != false) ? $child['regional'] = '' : $child['kpi'] = $child['bill_cycles'];
                        $del = [];
                        $del = ['a30s','a60s','a90s','a120s','ma30','ma60','ma90','ma120','mb30','mb60','mb90','mb120','ab30','ab60','ab90','ab120','bb30','bb60','bb90','bb120','bb30','bb60','bb90','bb120','mb30s','mb60s','mb90s','mb120s','ma120s','ma30s','ma60s','ma90s','ma120s','h120s','h30s','h60s','h90s','h120s','b30s','b60s','b90s','b120s','a30','h30','b30','a60','h60','b60','a90','h90','b90','a120','h120','b120','c30','c60','c90','c120','periodes','total','totalmsisdn','120','90','60','30'];
                        foreach($del as $childdel){
                            unset($child[$childdel]);

                        }
                        $sum[$region]['children'][] = $child;
                        $childn++;
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

    protected function countDataCekBayarChild($child,$ismom){

    }
    protected function countDataCekBayar($p,$row,$ismsisdn = false,$ischild = false, $ismom = false){
        $sum = [];
        $kpihelper = [120,90,60,30];
        //if($ischild AND $ismom) echo $p;
        switch($p) {
            case 0:
                $dataserah = $row['total'];
                $collection = $row['h30'] + $row['h60'] + $row['h90'] +  $row['h120'] ;
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
                $dataserah = $row['a30'];
                $collection = $row['h30'];
                $total = $dataserah;
                $nmsisdn = $row['ma30'];
                $bmsisdn = $row['mb30'];
                break;
            case 60:
                $dataserah = $row['a60'];
                $collection = $row['h60'];
                $nmsisdn = $row['ma60'];
                $bmsisdn = $row['mb60'];
                $total = $dataserah;
                break;
            case 90:
                $dataserah = $row['a90'];
                $collection = $row['h90'];
                $total = $dataserah;
                $nmsisdn = $row['ma90'];
                $bmsisdn = $row['mb90'];
                break;
            case 120:
                $dataserah = $row['a120'];
                $collection = $row['h120'];
                $total = $dataserah;
                $nmsisdn = $row['ma120'];
                $bmsisdn = $row['mb120'];
                break;
            default:
                $dataserah = $row['total'];
                $collection = $row['h30'] + $row['h60'] + $row['h90'] +  $row['h120'] ;
                $total = $dataserah;
                $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'] + $row['ma30s'] + $row['ma60s'] + $row['ma90s'] + $row['ma120s'];
                $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'] + $row['mb30s'] + $row['mb60s'] + $row['mb90s'] + $row['mb120s'];

            if ($ischild) {
                $dataserahx = $dataserah;
                $collection = $dataserah = [];
                foreach ($kpihelper as $kpilabel) {
                    $dataserah[$kpilabel] = $row['a' . $kpilabel];
                    $total[$kpilabel] = $dataserah[$kpilabel];
                    $collection[$kpilabel] = $row['h' . $kpilabel];
                }
            } else {
                $dataserah = $row['total'];
                $total = $dataserah;
                $collection = $row['h30'] + $row['h60']  + $row['h90'] +  $row['h120'] ;
            }

            $nmsisdn = $row['ma30'] + $row['ma60'] + $row['ma90'] + $row['ma120'];
            $bmsisdn = $row['mb30'] + $row['mb60'] + $row['mb90'] + $row['mb120'];
        }

        if($ischild){
        }
        $pcollection = 0;
        if($ismsisdn === true){
            $collection = $nmsisdn;
            $dataserah = $bmsisdn;
        }

        if(is_array($dataserah)){
            $uncollected = $pcollection = [];
            foreach($kpihelper as $kpilabel){
                $uncollected[$kpilabel] = $dataserah[$kpilabel] - $collection[$kpilabel];
                if($total[$kpilabel] > 0 AND $collection[$kpilabel] > 0){
                    $pcollection[$kpilabel] = ($collection[$kpilabel] / $dataserah[$kpilabel]);
                }
            }
        }else{
            $uncollected = (int) $dataserah - (int) $collection;
            if($total > 0 AND $collection > 0){
                $pcollection = ((int) $collection/ (int)$dataserah);
            }
        }

        if(is_array($dataserah)){
            foreach($kpihelper as $kpilabel){
                if($total[$kpilabel] > 0 AND $collection[$kpilabel] > 0) {
                    $sum[$kpilabel]['uncollected'] = number_format($uncollected[$kpilabel]);
                    $sum[$kpilabel]['pcollection'] = number_format(($pcollection[$kpilabel]) * 100, 2) . '%';
                    $sum[$kpilabel]['collection'] = number_format($collection[$kpilabel]);
                    $sum[$kpilabel]['totalmsisdn'] = number_format($dataserah[$kpilabel]);
                    $sum[$kpilabel]['total'] = $dataserah[$kpilabel];
                }
            }
        }else{
            $sum['uncollected'] = number_format($uncollected);
            $sum['pcollection'] = number_format(($pcollection)*100,2).'%';
            $sum['collection'] = number_format((int) $collection);
            $sum['totalmsisdn'] = number_format((int) $dataserah);
            $sum['total'] = $dataserah;
        }

        return $sum;
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