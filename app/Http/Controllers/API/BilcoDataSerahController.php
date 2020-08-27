<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\BillingCollectionPoc;
use App\Helpers\AppHelper;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\False_;
use phpDocumentor\Reflection\Types\True_;
use Rap2hpoutre\FastExcel\FastExcel;

class BilcoDataSerahController extends Controller
{
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
                'cmactive.customer_address as cb_address','bbs_city','cmactive.customer_city AS cb_city','aging.bbs_zip_code','aging.aging_cust_subtype','aging.bbs_pay_type',
                'aging.bbs_RT','aging.bill_amount_04','aging.bill_amount_03','aging.bill_amount_02','aging.bill_amount_01',
                'aging.bucket_4','aging.bucket_3','aging.bucket_2','aging.bucket_1','aging.aging_status_subscribe','aging.blocking_status','aging.note','cmactive.customer_phone');
            if($tahap == 1){
                $x->where(function($query) {
                        $query->where(function ($query) {
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
                $query->orWhere('aging.bill_cycle', '=', 1);
                $query->orWhere('aging.bill_cycle', '=', 6);
                $query->orWhere('aging.bill_cycle', '=', 11);
            })
            /*->where(function($query){
                $query->orWhere('aging.bbs_RT', '=', 'PP');
                $query->orWhere('aging.bbs_RT', '=', '');
                $query->orWhereNull('aging.bbs_RT');
            })*/

            ->groupBy('aging.account')
            ->orderBy('aging.account');
        //DB::getQueryLog();
        //dd($x);
        return $x;
    }

    public function export() {
        $x= BilcoDataSerah::where('import_batch',1834)->get()->makeHidden(['import_batch']);

        $x = collect($x);
        return (new FastExcel($x))->download('file.xlsx');
    }

    public function getKpi(Request $request){
        $selectbillcycle = $end = $date = null;
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
        if($request->has('outs') == false){
            $d30harea->groupBy('kpi');
        }
        $d30harea->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->orderBy('regional','DESC')
            ->orderBy('kpi','ASC');
        $d30harea = $d30harea;
        $d30h = BilcoDataSerah::selectRaw('
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'.$selectbillcycle.'kpi,
        regional')
            ->groupBy('periodes','regional');
        if($request->has('outs') === false){
            $d30h->groupBy('kpi');
        }
            $d30h->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->orderBy('regional','DESC')
            ->orderBy('kpi','ASC');
        $d30h =$d30h->union($d30harea)->get()->toArray();
        $d90harea = BilcoDataSerah::selectRaw('
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'
            .$selectbillcycle.
        'bill_cycle as kpi,
        "AREA Sumatra" AS regional')
            ->groupBy('periodes');
        if($request->has('outs') === false){
            $d90harea->groupBy('kpi');
        }
        $d90harea->groupBy('bill_cycle')
            ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->orderBy('regional','DESC')
            ->orderBy('kpi','ASC')
            ->orderBy('bill_cycle','ASC');
        $d90harea =$d90harea;

        $d90h = BilcoDataSerah::selectRaw('
        sum(total_outstanding) as total,
        count(msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         kpi as kpis,'.$selectbillcycle.'
        bill_cycle as kpi,
        regional')
            ->groupBy('periodes','regional');
            if($request->has('outs') === false){
                $d90h->groupBy('kpi');
            }
            $d90h->groupBy('bill_cycle')
                ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->orderBy('regional','DESC')
            ->orderBy('kpi','ASC')
            ->orderBy('bill_cycle','ASC');
        //dd($d90h->get()->toArray());
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
        foreach ($d30h as $row){
            $i = $row;
            $row['total'] = number_format($row['total']);
            $row['id'] = sprintf('%s#%s#%s#%s',$l,$row['regional'],$row['periodes'],$row['kpi']);
            if($request->has('outs') === true){
                $row['kpi'] = '*';
            }
            $l++;
            $row['totalmsisdn'] = number_format($row['totalmsisdn']);
            foreach ($period as $p){
                if($p === (string) $row['periodes']){
                    $row[$p]['total'] = $row['total'];
                    $row[$p]['totalmsisdn'] = $row['totalmsisdn'];
                }else{
                    $row[$p] = null;
                }
            }
            //var_dump($row);
            foreach ($d90h as $child){
               /// var_dump($child);
                $cekkpi = false;
                if($child['kpis'] === $row['kpis']){
                    $cekkpi = true;
                }
                if($request->has('outs') === true){
                    $cekkpi = true;
                }
                if($child['periodes'] === $row['periodes'] && $cekkpi === true && $child['regional'] == $row['regional'] ) {
                    //var_dump($child);
                    unset($child['kpis']);
                    $lc = 0;
                    foreach ($period as $p){
                        $lc = $lc+1;
                        $child['totalmsisdn'] = number_format($child['totalmsisdn']);
                        $child['total'] = number_format($child['total']);
                        if($p === (string) $child['periodes']){
                            $child['id'] = sprintf('sub#%s#%s#%s#%s#%s',$l,$lc,$child['regional'],$child['periodes'],$child['kpi']);
                            $child[$p]['total'] = $child['total'];
                            $child[$p]['totalmsisdn'] = $child['totalmsisdn'];
                        }else{
                            $child[$p] = null;
                        }
                    }
                    $row['children'][] = $child;
                }
            }
            unset($row['kpis']);
            $temp[] = $row ;
        }

        return datatables()->of($temp)->with('datecolumn',$period)->toJson();
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
        $d90harea = BilcoDataSerah::selectRaw('count( msisdn) as msisdn,
	sum( bucket_1 ) as bucket_1,
	sum( bucket_2 ) as bucket_2,
	sum( bucket_3 ) as bucket_3,
	sum( bucket_4 ) as bucket_4,
        "Area Sumatera" as regional,
	sum(total_outstanding) as total_outstanding')
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')));
        $d90h = BilcoDataSerah::selectRaw('count( msisdn) as msisdn,
	sum( bucket_1 ) as bucket_1,
	sum( bucket_2 ) as bucket_2,
	sum( bucket_3 ) as bucket_3,
	sum( bucket_4 ) as bucket_4,
        regional,
	sum(total_outstanding) as total_outstanding')
            ->groupBy('regional')
            ->whereBetween('periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'))
            ->union($d90harea);
        $temp = array();
            foreach ($d90h->get()->toArray() as $row)
            {
                $row['bucket_1'] = number_format($row['bucket_1']);
                $row['bucket_2'] = number_format($row['bucket_2']);
                $row['bucket_3'] = number_format($row['bucket_3']);
                $row['bucket_4'] = number_format($row['bucket_4']);
                $row['total_outstanding'] = number_format($row['total_outstanding']);
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
