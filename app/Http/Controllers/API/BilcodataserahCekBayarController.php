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
    public function index(Request $request){
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
            $end = Carbon::createFromFormat('Y-m', $end)->addDay(180);
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
            $selectbillcycle = 'bilco_data_serahs.bill_cycle as bill_cycles,';
        }

        $d30harea = BilcodataserahCekBayar::selectRaw('
        sum(bilco_data_serahs.total_outstanding) as total,
        count(bilco_data_serahs.msisdn) as totalmsisdn,
         DATE_FORMAT(DATE_ADD(bilco_data_serahs.periode, INTERVAL 2 DAY),"%m-%Y") as periodes,
         bilco_data_serahs.kpi as kpis,'.$selectbillcycle.'kpi,
        "AREA Sumatra" AS regional')
            ->Join('bilco_data_serahs as bilco_data_serahs', function($join)
            {
                $join->on('bilcodataserah_cek_bayars.account', '=', 'bilco_data_serahs.account');
                $join->on('bilcodataserah_cek_bayars.periode', '=', 'bilco_data_serahs.periode');
            })->limit(1);
        $d30harea->get();
        dd(DB::getQueryLog());
        dd($d30harea->get()->toArray());
        if($bill_cycle!=null){
            $d30harea->where('bill_cycle',$bill_cycle);
        }
        if(sizeof($tahap)>0){
            $d30harea->where(function($query)use($tahap) {
                foreach ($tahap as $thpd){
                    $query->orwhere('bilco_data_serahs.periode',$thpd->format('Y-m-d'));
                }
            });
        }
        $d30harea->whereBetween('bilco_data_serahs.periode',array($date->format('Y-m-d'),$end->format('Y-m-d')))
            ->orderBy('bilco_data_serahs.hlr_region','DESC')
            ->orderBy('bilco_data_serahs.kpi','ASC');

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
                            $child[$p]['total'] = $child['total'];
                            $child[$p]['totalmsisdn'] = $child['totalmsisdn'];
                            $child['period'][$p]['total'] = number_format($child['total']);
                            $child['period'][$p]['totalmsisdn'] = number_format($child['totalmsisdn']);
                        }else{
                            $child[$p] = null;
                        }
                    }
                    $row['children'][$row['kpi']][] = $child;
                    $region = $child['regional'];
                    if($request->has('outs') === false){
                        $child['regional'] = '';
                    }else{
                        $child['kpi'] = $child['bill_cycles'];
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
