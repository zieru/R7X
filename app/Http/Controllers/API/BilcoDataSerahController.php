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
                })
                ->select('bilco.account_number','bilco.periode','bilco.msisdn','bilco.bill_cycle','bilco.regional','bilco.bucket_4','bilco.bucket_3','bilco.bucket_2','bilco.bucket_1')
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
