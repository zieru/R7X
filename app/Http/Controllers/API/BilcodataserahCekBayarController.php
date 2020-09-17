<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BilcodataserahCekBayar;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class BilcodataserahCekBayarController extends Controller
{
    public function fetch(){
        DB::enableQueryLog();
        $date = '20200831';
        $adate = Carbon::createFromFormat('Ymd', $date);
        $bdate = Carbon::createFromFormat('Ymd', $date)->addDay(1);
        $x= DB::table('sabyan_r7s.bilco_data_serahs AS a')
            ->select('a.periode',
                    'a.account',
                    'a.bucket_1 as a30',
                    'a.bucket_2 as a60',
                    'a.bucket_3 as a90',
                    'a.bucket_4 as a120',
                    'b.bucket_1 as b0',
                    'b.bucket_2 as b30',
                    'b.bucket_3 as b60',
                    'b.bucket_4 as b90',
                    'b.bucket_5 as b120'
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
    public function index()
    {
        //
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
