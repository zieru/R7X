<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\BillingCollectionPoc;
use App\Helpers\AppHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidFormatException;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\False_;
use phpDocumentor\Reflection\Types\True_;
use Ramsey\Uuid\Type\Decimal;
use Rap2hpoutre\FastExcel\FastExcel;
use URL;

class BilcoDataSerahController extends Controller
{
    public function chart(Request $req){

        if($req->get('tipe') == 'msisdn2'){
            $x = BilcoDataSerah::select('periode', DB::raw('count(msisdn) as value'),DB::raw('"AREA I" as hlr_region'))->groupBy('periode');
            $y = BilcoDataSerah::select('periode', DB::raw('count(msisdn) as value'),'hlr_region')->groupBy('hlr_region','periode')->union($x);
        }
        if($req->get('tipe') == 'outs2'){
            $x = BilcoDataSerah::select('periode', DB::raw('sum(total_outstanding) as value'),DB::raw('"AREA I" as hlr_region'))->groupBy('periode');
            $y = BilcoDataSerah::select('periode',DB::raw('sum(total_outstanding) as value'),'hlr_region')->groupBy('hlr_region','periode')->union($x);
        }

        $ret = [];
        foreach($y->get()->toArray() as $row){
            $row['value'] = number_format($row['value']);
            $ret[] = $row;
        }
        if($req->get('tipe') == 'msisdn2' OR $req->get('tipe') == 'outs2'){
            $ret = [];
            foreach($y->get()->toArray() as $row){
               // $row['value'] = number_format($row['value']);
                $periode = Carbon::createFromFormat('Y-m-d', $row['periode']);
                if(in_array($periode->format('d'),array(28,29,30,31))){
                    $periode->addDay(1);
                }
                $ret[$periode->format('Y-m')]['periode'] = $periode->format('Y-m');
                if(!isset($ret[$periode->format('Y-m')]['Sumbagut'])){
                    $ret[$periode->format('Y-m')]['Sumbagut'] = 0;
                }
                if(!isset($ret[$periode->format('Y-m')]['Sumbagsel'])){
                    $ret[$periode->format('Y-m')]['Sumbagsel'] = 0;
                }
                if(!isset($ret[$periode->format('Y-m')]['Sumbagteng'])){
                    $ret[$periode->format('Y-m')]['Sumbagteng'] = 0;
                }
                $ret[$periode->format('Y-m')][$row['hlr_region']] = number_format($row['value']);
            }
        }
        sort($ret);
        return datatables()->of($ret)->toJson();
    }

    public function fetch($date){
        DB::enableQueryLog();
        $bilcoenddate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2)->endOfMonth();
        $bilcodate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2);
        $agingdate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-1);
        $tahap = false;
        switch ($bilcodate->format('d')){
            case $bilcoenddate->format('d'):
                $tahap = 1;
                    break;
            default:
                $tahap = 2;
        }
//$agingdate = $bilcodate = $date;
        echo(sprintf('tahap :%s bilco : %s Aging : %s',$tahap,$bilcodate->format('Ymd'),$agingdate->format('Ymd')));
//die();
        $x= DB::table('sabyan_r7s_data.'.$bilcodate->format('Ymd').'_Sumatra as bilco')
            ->leftJoin('olala2.aging_'.$agingdate->format('Ymd').' as aging', function($join)
            {
                $join->on('bilco.account_number','=','aging.account');
            },'left outer')
            ->leftJoin('olala2.cm_active_unique as cmactive', function($join)
            {
                $join->on('aging.customer_id','=','cmactive.customer_id');
            },'left outer')
            ->select('aging.account','aging.customer_id','bilco.periode','aging.msisdn','aging.bill_cycle','aging.regional','aging.grapari','bilco.regional AS hlr_region','aging.hlr_city','aging.bbs','aging.bbs_name','aging.bbs_company_name','aging.bbs_first_address','aging.bbs_second_address',
                'cmactive.customer_address as cb_address','bbs_city','cmactive.customer_city AS cb_city','aging.bbs_zip_code','aging.bbs_pay_type',
                'aging.bbs_RT','aging.bill_amount_04','aging.bill_amount_03','aging.bill_amount_02','aging.bill_amount_01',
                'aging.bucket_4','aging.bucket_3','aging.bucket_2','aging.bucket_1','aging.blocking_status','aging.note','cmactive.customer_phone');
            if($tahap == 1){
                $x->where(function($query) {
                        $query->where(function ($query)     {
                            $query
                                ->where('aging.bucket_3', '>', 0)
                                ->Where('aging.bucket_2', '>', 0);
                        });
                        $query->orWhere(function ($query) {
                            $query
                                ->where('aging.bucket_2','>',12500)
                                ->Where('aging.bucket_1','>',0);
                        });
                    });

                    $x
                    ->where('aging.bucket_5','<=',0)
                    ->where('aging.bucket_6','<=',0)
                    ->where('aging.bucket_4','<=',0)
                    ->where('aging.osbalance','>=',50000);
            }elseif($tahap == 2){
                $x->where('aging.bucket_2','>',12500)
                    ->where('aging.bucket_1','>',0)
                    ->where('aging.bucket_3','<=',0)
                    ->where('aging.bucket_4','<=',0)
                    ->where('aging.total_outstanding','>=',50000);
            }

            $x->where('aging.aging_cust_subtype','=','Consumer Reguler')
            ->where(function($query){
                $query->orWhere('aging.bbs_RT', '=', 'PP');
                $query->orWhere('aging.bbs_RT', '=', '');
                $query->orWhereNull('aging.bbs_RT');
            })

            ->groupBy('aging.account')
            ->orderBy('aging.account');
        //DB::getQueryLog();
        //dd($x);
        return $x;
    }

    public function export(Request $req) {
        $tahap = $req->get('tahap');
        $start = $req->get('amp;start');
        $end = null;
        $periode = array(Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-1)->format('Y-m-d'),Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(12)->format('Y-m-d'));
        switch($tahap){
            case '1':
                $periode = array(Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-1)->format('Y-m-d'),Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-1)->format('Y-m-d'));
                break;
            case '2':
                $periode = array(Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(14)->format('Y-m-d'),Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(14)->format('Y-m-d'));
                break;
        }
        $regional = $req->get('amp;regional');
        $x= BilcoDataSerah::
        where('hlr_region',$regional)
            ->whereBetween('periode',$periode)
            ->get()->makeHidden(['import_batch']);

        $x = collect($x);
        return (new FastExcel($x))->download('file.xlsx', function ($row) {
            return [
                'tahap' => $row->tahap,
                'account' => $row->account,
                'customer_id' => $row->customer_id,
                'msisdn' => $row->msisdn,
                'bill_cycle' => $row->bill_cycle,
                'regional' => $row->regional,
                'grapari' => $row->grapari,
                'hlr_region' => $row->hlr_region,
                'hlr_city' => $row->hlr_city,
                'bbs' => $row->bbs,
                'bbs_name' => $row->bbs_name,
                'bbs_company_name' => $row->bbs_company_name,
                'bbs_first_address' => $row->bbs_first_address,
                'bbs_second_address' => $row->bbs_second_address,
                'cb_address' => $row->cb_address,
                'cb_city' => $row->cb_city,
                'bbs_city' => $row->bbs_city,
                'bbs_zip_code' => $row->bbs_zip_code,
                'bbs_pay_type' => $row->bbs_pay_type,
                'bbs_RT' => $row->bbs_RT,
                'bill_amount_04' => $row->bill_amount_04,
                'bill_amount_03' => $row->bill_amount_03,
                'bill_amount_02' => $row->bill_amount_02,
                'bill_amount_01' => $row->bill_amount_01,
                'bucket_4' => $row->bucket_4,
                'bucket_3' => $row->bucket_3,
                'bucket_2' => $row->bucket_2,
                'bucket_1' => $row->bucket_1,
                'total_outstanding' => $row->total_outstanding,
                'kpi' => $row->kpi,
                'blocking_status' => $row->blocking_status,
                'note' => $row->note,
                'customer_phone' => $row->customer_phone,
                'cek_halo' => $row->cek_halo,
                'cek_cp' => $row->cek_cp
            ];
        });
    }

    public function getKpi(Request $request){
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

        $d30harea = BilcoDataSerah::selectRaw('
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
        $d30h = BilcoDataSerah::selectRaw('
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
        $d90harea = BilcoDataSerah::selectRaw('
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'
            .$selectbillcycle.
        'kpi as kpi,
        "AREA Sumatra" AS regional')
            ->groupBy('periodes');
        if($request->has('outs') === false){
            $d90harea->groupBy('kpi');
        }
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

        }
        $d90harea->groupBy('bill_cycle')
            ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('hlr_region','DESC')
            ->orderBy('kpi','ASC')
            ->orderBy('bill_cycle','ASC');
        $d90harea =$d90harea;

        $d90h = BilcoDataSerah::selectRaw('
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
        $d90h =$d90h->union($d90harea)->get()->toArray();
        //dd($d90h);
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

        foreach ($d30h as $row){

            $row['total'] = number_format($row['total']);
            $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
            if($request->has('outs') === true){
                $row['kpi'] = 'All BC';
            }
            if($request->has('outs') === false){
                $row['kpi'] = 'All KPI';
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
            foreach ($period as $p){
                if($p === (string) $row['periodes']){
                    $row['x'][$p]['total'] = $row['total'];
                    $row['x'][$p]['totalmsisdn'] = $row['totalmsisdn'];

                    $sum[$row['regional']]['period'][$p]['totalmsisdn'] = $row['totalmsisdn'];
                    $sum[$row['regional']]['period'][$p]['total'] = $row['total'];
                }
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
                    $child['kpi'] =$child['kpis'];
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
                            $child[$p]['total'] = $child['total'];
                            $child[$p]['totalmsisdn'] = $child['totalmsisdn'];
                            $child['period'][$p]['total'] = number_format($child['total']);
                            $child['period'][$p]['totalmsisdn'] = number_format($child['totalmsisdn']);
                        }else{
                            $child[$p] = null;
                        }
                    }
                    $row['children'][] = $child;
                    $region = $child['regional'];
                    if($request->has('outs') === false){
                        $child['regional'] = '';
                    }
                    $sum[$region]['children'][] = $child;
                }
            }
            unset($row['kpis']);
            $i[$row['regional']]=$row;
            $temp[] = $row ;
        }

        return datatables()->of($sum)->with('datecolumn',$period)->toJson();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $end = $date = null;
        $tahap_d = $request->get('tahap');
        $start = explode(':',$request->get('periode'))[0];
        $end = null;
        try {
            $date = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        $tahap = null;
        if($tahap_d > 0){
            if($tahap_d == 1){
                $tahap = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(-1);
            }else{
                $tahap = Carbon::createFromFormat('Y-m-d', $start.'-01')->addDay(12);
            }
        }
        $d90harea = BilcoDataSerah::selectRaw('count( msisdn) as msisdn,
	sum( bucket_1 ) as bucket_1,
	sum( bucket_2 ) as bucket_2,
	sum( bucket_3 ) as bucket_3,
	sum( bucket_4 ) as bucket_4,
        "Area Sumatera" as regional,
	sum(total_outstanding) as total_outstanding')
            ->whereIn('hlr_region',array('Sumbagut','Sumbagteng','Sumbagsel'));
        if($tahap != null)$d90harea->where('periode',$tahap->format('Y-m-d'));

        $d90h = BilcoDataSerah::selectRaw('count( msisdn) as msisdn,
	sum( bucket_1 ) as bucket_1,
	sum( bucket_2 ) as bucket_2,
	sum( bucket_3 ) as bucket_3,
	sum( bucket_4 ) as bucket_4,
        hlr_region as regional,
	sum(total_outstanding) as total_outstanding')
            ->groupBy('hlr_region')
            ->whereIn('hlr_region',array('Sumbagut','Sumbagteng','Sumbagsel'));
        if($tahap != null)$d90h->where('periode',$tahap->format('Y-m-d'));
        $d90h->union($d90harea);
        $temp = array();
            foreach ($d90h->get()->toArray() as $row)
            {
                $row['bucket_1'] = number_format($row['bucket_1']);
                $row['bucket_2'] = number_format($row['bucket_2']);
                $row['bucket_3'] = number_format($row['bucket_3']);
                $row['bucket_4'] = number_format($row['bucket_4']);
                $row['total_outstanding'] = number_format($row['total_outstanding']);
                $row['download'] = sprintf('%s?tahap=%s&start=%s&regional=%s',URL::to('/external/bilcodataserahexport'),$tahap_d,$start,$row['regional']);
                $temp[] = $row;
            }
            return datatables()->of($temp)->toJson();
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
     * @param  \App\BilcoDataSerah  $bilcoDataSerah
     * @return \Illuminate\Http\Response
     */
    public function show(BilcoDataSerah $bilcoDataSerah)
    {

        dd($bilcoDataSerah);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BilcoDataSerah  $bilcoDataSerah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BilcoDataSerah $bilcoDataSerah)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BilcoDataSerah  $bilcoDataSerah
     * @return \Illuminate\Http\Response
     */
    public function destroy(BilcoDataSerah $bilcoDataSerah)
    {
        //
    }
}
