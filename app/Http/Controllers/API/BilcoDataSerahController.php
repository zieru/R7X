<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use App\BillingCollectionPoc;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BilcoDataSerahController extends Controller
{
    public function fetch($date){
        DB::enableQueryLog();
$bilcodate = $date;
$agingdate = $date->addDays(1);

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
            ->where(function($query){
                $query->orWhere('aging.bill_cycle', '=', 1);
                $query->orWhere('aging.bill_cycle', '=', 6);
                $query->orWhere('aging.bill_cycle', '=', 11);
            })
            ->get();
        //dd(DB::getQueryLog());
        return $x;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $d90h = BilcoDataSerah::selectRaw('count( msisdn ),
	sum( bucket_1 ),
	sum( bucket_2 ),
	sum( bucket_3 ),
	sum( bucket_4 ),
	regional,
	sum(total_outstanding)')->groupBy('regional');
            return datatables()->of($d90h);
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
        //
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
