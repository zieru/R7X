<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\BillingCollectionPoc;
use App\Helpers\AppHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BilcoDataSerahController extends Controller
{
    public function fetch($date){
        DB::enableQueryLog();
//$agingdate = $bilcodate = $date;
$bilcodate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-2); 
$agingdate = Carbon::createFromFormat('Ymd', $date->format('Ymd'))->addDays(-1);
echo sprintf('bilco : %s Aging : %s',$bilcodate->format('Ymd'),$agingdate->format('Ymd'));
//die();
        $x= DB::table('olala2.cm_active_unique as cmactive')
            ->leftJoin('olala2.aging_'.$agingdate->format('Ymd').' as aging', function($join)
            {
                $join->on('cmactive.customer_id','=','aging.customer_id');
            })
            ->leftJoin('sabyan_r7s_data.'.$bilcodate->format('Ymd').'_Sumatra as bilco', function($join)
            {
                $join->on('aging.account','=','bilco.account_number');
            })
            ->select('aging.account','aging.customer_id','bilco.periode','aging.msisdn','aging.bill_cycle','aging.regional','aging.grapari','bilco.regional AS hlr_region','aging.hlr_city','aging.bbs','aging.bbs_name','aging.bbs_company_name','aging.bbs_first_address','aging.bbs_second_address',
                'cmactive.customer_address as cb_address','bbs_city','cmactive.customer_city AS cb_city','aging.bbs_zip_code','aging.aging_cust_subtype','aging.bbs_pay_type',
                'aging.bbs_RT','aging.bill_amount_04','aging.bill_amount_03','aging.bill_amount_02','aging.bill_amount_01',
                'aging.bucket_4','aging.bucket_3','aging.bucket_2','aging.bucket_1','aging.aging_status_subscribe','aging.blocking_status','aging.note','cmactive.customer_phone')
            ->where('aging.bucket_2','>',12500)
            ->where('aging.bucket_1','>',0)
            ->where('aging.bucket_3','<=',0)
            ->where('aging.bucket_4','<=',0)
            ->where('aging.total_outstanding','>=',50000)
            ->where('aging.aging_cust_subtype','=','Consumer Reguler')
            ->where(function($query){
                $query->orWhere('aging.bill_cycle', '=', 1);
                $query->orWhere('aging.bill_cycle', '=', 6);
                $query->orWhere('aging.bill_cycle', '=', 11);
            })
            ->where(function($query){
                $query->orWhere('aging.bbs_RT', '=', 'PP');
                $query->orWhere('aging.bbs_RT', '=', '');
                $query->orWhereNull('aging.bbs_RT');
            })
            ->groupBy('aging.account')
            ->orderBy('aging.customer_id');
        //dd($x);
        return $x;
    }

    public function getKpi(Request $request){
        $date = null;
        try {
            $date = Carbon::createFromFormat('Y-m-d', $request->get('period'))->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        $d30h = BilcoDataSerah::selectRaw('
        sum(total_outstanding) as date1,
        regional')->fromSub(function ($query) use($request) {
                $query->selectRaw('kpi')->where('kpi','30-90');
            }, 'sub')
            ->groupBy('regional')
            ->where('periode',$date->format('Y-m-d'))
            ->where('kpi','30-60')
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'));
        dd(datatables()->of($d30h)->toArray()['data']);
        return datatables()->of($d30h)->toJson();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $date = null;
        try {
            $date = Carbon::createFromFormat('Y-m-d', $request->get('period'))->addDay(-2);
        }
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}
        $d90h = BilcoDataSerah::selectRaw('count( msisdn) as msisdn,
	sum( bucket_1 ) as bucket_1,
	sum( bucket_2 ) as bucket_2,
	sum( bucket_3 ) as bucket_3,
	sum( bucket_4 ) as bucket_4,
        regional,
	sum(total_outstanding) as total_outstanding')
            ->groupBy('regional')
            ->where('periode',$date->format('Y-m-d'))
            ->whereIn('regional',array('Sumbagut','Sumbagteng','Sumbagsel'));
            return datatables()->of($d90h)->toJson();
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
