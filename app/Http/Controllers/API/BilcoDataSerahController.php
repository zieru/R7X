<?php

namespace App\Http\Controllers\API;

use App\BilcoDataSerah;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BilcoDataSerahController extends Controller
{
    public function fetch(){
        return DB::table('sabyan_r7s_data.20200803_all as bilco')
                ->join('olala2.aging_20200802 as aging', function($join)
                {
                    $join->on('aging.msisdn', '=', 'bilco.msisdn');
                    $join->on('aging.account','=','bilco.account_number');
                    $join->on('aging.bill_cycle','=','bilco.bill_cycle');
                })
                ->join('olala2.cm_active_unique as cmactive', function($join)
                {
                    $join->on('cmactive.msisdn', '=', 'bilco.msisdn');
                    $join->on('cmactive.customer_id','=','bilco.account_number');
                    $join->on('cmactive.billcycle','=','bilco.bill_cycle');
                })
                ->select('aging.account','aging.customer_id','bilco.periode','aging.msisdn','aging.bill_cycle','bilco.regional','bilco.bucket_4','bilco.bucket_3','bilco.bucket_2','bilco.bucket_1',
                    'aging.grapari','aging.hlr_city','aging.bbs','aging.bbs_name','aging.bbs_company_name','aging.bbs_first_address','aging.bbs_second_address',
                    'cmactive.customer_address','bbs_city','cmactive.customer_city','aging.bbs_zip_code','aging.aging_cust_subtype','aging.bbs_pay_type',
                    'aging.bbs_RT','aging.aging_status_subscribe','aging.blocking_status','aging.note','cmactive.customer_phone')
                ->whereIn('bilco.regional', array('Sumbagut','Sumbagsel','Sumbagteng'))
                ->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
