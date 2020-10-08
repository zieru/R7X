<?php

namespace App\Http\Controllers;
use App\Helpers\AppHelper;
use Rap2hpoutre\FastExcel\FastExcel;

use App\Notifier;
use DateTime;
use Illuminate\Support\Collection;
use App\BillingCollection;
use App\BillingCollectionPoc;
use App\BillingCollectionTarget;
use App\Importer;
use DB;
Use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Storage;
use test\Mockery\Fixtures\MethodWithVoidReturnType;


class BillingCollectionController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
/*
        (SUM(`bill_amount_3`) - sum(`bucket_3`)) / `bill_amount_3`  as '60hCOLP'
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / `bill_amount_3`  as '90hCOLP'*/

    }

    public function compactPOC($periode){

        $d90h = BillingCollection::groupBy('regional','bill_cycle')
            ->selectRaw('
            import_batch,
            periode,
            area,
            regional,
            bill_cycle,
    sum( bill_amount_1 ) AS bill_amount_1,
	sum( bill_amount_2 ) AS bill_amount_2,
	sum( bill_amount_3 ) AS bill_amount_3,
	sum( bill_amount_4 ) AS bill_amount_4,
	sum( bill_amount_5 ) AS bill_amount_5,
	sum( bill_amount_6 ) AS bill_amount_6,
	sum( bill_amount_7 ) AS bill_amount_7,
	sum( bill_amount_8 ) AS bill_amount_8,
	sum( bill_amount_9 ) AS bill_amount_9,
	sum( bill_amount_10 ) AS bill_amount_10,
	sum( bill_amount_11 ) AS bill_amount_11,
	sum( bill_amount_12 ) AS bill_amount_12,
	sum( bucket_1 ) AS bucket_1,
	sum( bucket_2 ) AS bucket_2,
	sum( bucket_3 ) AS bucket_3,
	sum( bucket_4 ) AS bucket_4,
	sum( bucket_5 ) AS bucket_5,
	sum( bucket_6 ) AS bucket_6,
	sum( bucket_7 ) AS bucket_7,
	sum( bucket_8 ) AS bucket_8,
	sum( bucket_9 ) AS bucket_9,
	sum( bucket_10 ) AS bucket_10,
	sum( bucket_11 ) AS bucket_11,
	sum( bucket_12 ) AS bucket_12,
	sum( bucket_13 ) AS bucket_13,
	sum( total_bucket_per_msisdn ) AS total_bucket_per_msisdn
                                    ')
            ->where('periode','=',$periode)
            ->where('customer_type','=','S')
            ->orderBy('bill_cycle','DESC')
            ->orderBy('periode','ASC');
        //echo 'x';
        //var_dump($d90h);

      //dd($d90h->get()->toArray());
      //dd(DB::getQueryLog());
        foreach ($d90h->get()->toArray() as $d){
            BillingCollectionPoc::create($d);
        }
        BillingCollection::truncate();
    }

    public function dashboardApi(Request $request){
        $d60h = BillingCollectionPOC::
            periode($request->get('start'))
            ->customerType('S')
            ->d60hRegional()
            ->billingNonZero();
        $d90h = BillingCollectionPoc::
            periode($request->get('start'))
            ->customerType('S')
            ->d90hRegional()
            ->billingNonZero()
            ->union($d60h);

        $bilco = datatables()->of($d90h);

        if($request->has('export')){
            $arr = ($bilco->toArray())['data'];
            $list = new Collection();
            $list = collect($arr);
            return (new FastExcel($list))->download('file.'.$request->get('export'));
        }else{
            return $bilco->toJson();
        }
        return false;
    }
    public function dashboardApiArea(Request $request){
        $d60h = BillingCollectionPOC::periode($request->get('start'))
            ->customerType('S')
            ->d60hArea()
            ->billingNonZero();
        $d90h = BillingCollectionPOC::
            periode($request->get('start'))
            ->customerType('S')
            ->d90hArea()
            ->billingNonZero()
            ->union($d60h);

        $bilco = datatables()->of($d90h);
        //$result = json_decode((string) $bilco, true);
        $result = $bilco;
        //return response()->json($result, $this->successStatus);

        if($request->has('export')){
            $arr = ($bilco->toArray())['data'];
            $list = new Collection();
            $list = collect($arr);
            return (new FastExcel($list))->download('file.'.$request->get('export'));
        }else{
            return $bilco->toJson();
        }
        return false;
    }

  /**
   * Multi-array search
   *
   * @param array $array
   * @param array $search
   * @return array
   */
  function multi_array_search($array, $search)
  {

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value)
    {

      // Iterate over each search condition
      foreach ($search as $k => $v)
      {

        // If the array element does not meet the search condition then continue to the next element
        if (!isset($value[$k]) || $value[$k] != $v)
        {
          continue 2;
        }

      }

      // Add the array element's key to the result array
      $result[] = $key;

    }

    // Return the result array
    return $result;

  }


    public function targetDateYear(Request $request){
      $x = BillingCOllectionTarget::select('*')->whereBetween('periode',[sprintf('%s-01',$request->get('start')),sprintf('%s-31',$request->get('start'))]);
        $bilco = datatables()->of($x);
      return $bilco->toJson();
    }
    public function targetDateYearPost(Request $request){
      $date = sprintf('%s-01',$request->post('periode'));
      $arr = array();
      if($request->has('sumbagut')){
          $arr[] = array('periode' => $date,
              'target' => $request->post('sumbagut'),
              'regional' => 'Sumbagut'
          );
      }
        if($request->has('sumbagteng')){
            $arr[] = array('periode' => $date,
                'target' => $request->post('sumbagteng'),
                'regional' => 'Sumbagteng'
            );
        }
        if($request->has('sumbagsel')){
            $arr[] = array('periode' => $date,
                'target' => $request->post('sumbagsel'),
                'regional' => 'Sumbagsel'
            );
        }
        if($request->has('area1')){
            $arr[] = array('periode' => $date,
                'target' => $request->post('area1'),
                'regional' => 'AREA1'
            );
        }


        foreach($arr as $i){
            var_dump($i);
            BillingCollectionTarget::updateOrCreate(
                [
                    'periode' => $i['periode'],
                    'regional' => $i['regional']
                ],
                [
                    'target' => $i['target']
                ]
            );
        }
    }
  public function dashboardApiTargetxs(Request $request){
    ini_set('precision', 15);
    $bc = false;
    $d = new DateTime($request->get('periode'));
    $d->modify('first day of this month');
    $d90h = BillingCollectionPoc::selectRaw('
                                        "REGIONAL" AS LABEL,
                                        CONCAT(bc.regional) AS regional,
                                        area,
                                        bc.regional AS subarea,
                                        bc.bill_cycle,
                                        sum( bc.bill_amount_2 ) AS billing_2,
                                        sum( bc.bill_amount_3 ) AS billing_3,
                                        sum( bc.bucket_2 ) AS bucket_2,
                                        sum( bc.bucket_3 ) AS bucket_3,
                                        billing_collections_targets.target as target  
                                    ')
      ->from('billing_collections_poc','bc')
      ->leftjoin('billing_collections_targets','bc.regional','=','billing_collections_targets.regional')
      ->orderBy('billing_2','DESC')
      ->groupBy( 'bc.regional')
      ->where('billing_collections_targets.periode','=' ,$d->format('Y-m-d'))
      ->where('bc.periode','=' ,$request->get('end'))
      ->where('bc.regional','!=' ,'**************')
      ->where('bc.area','=' ,'AREA I')
      ->where('bc.customer_type','=','S');
    $d90harr =  $d90h->get()->toArray();
    $temp = array();
    $target = (float) 0.974820372699075;
    $area = array();
    foreach ($d90harr as $row)
    {
      $target = (float) $row['target'];

      $x= array();
      $x['collection_60h'] = $row['billing_2'] - $row['bucket_2'];
      $x['perfomansi_60h'] = $x['collection_60h'] / $row['billing_2'];
      $x['perfomansi_60h_gap'] = $target - $x['perfomansi_60h'];
      $x['collection_90h'] = $row['billing_3'] - $row['bucket_3'];
      $x['perfomansi_90h'] = $x['collection_90h'] / $row['billing_3'];
      $x['perfomansi_90h_gap'] = $target - $x['perfomansi_90h'];
      /*$temp[] = array(
          'regional' => $row['regional'],
          'kpi' => '60h',
          'area' => $row['area'],
          'bill_cycle' => $row['bill_cycle'],
          'billing' => (float) $row['billing_2'],
          'bucket' => (float) $row['bucket_2'],
          'collection' => $x['collection_60h'],
          'perfomansi' => $x['perfomansi_60h'],
          'perfomansi_target' => number_format($target*100,2),
          'perfomansi_percent' => number_format($x['perfomansi_60h']*100,2),
          'perfomansi_gap' => number_format($x['perfomansi_60h_gap']*100,2),
          'perfomansi_nominal' => number_format($x['perfomansi_60h_gap'] * $row['billing_2']),
          'target' => $target,
      );*/
      $temp[] = array(
        'regional' => $row['regional'],
        'kpi' => '90h',
        'area' => $row['area'],
        'bill_cycle' => $row['bill_cycle'],
        'billing' => (float) $row['billing_3'],
        'bucket' => (float) $row['bucket_3'],
        'collection' => $x['collection_90h'],
        'perfomansi' => $x['perfomansi_90h'],
        'perfomansi_target' => number_format($target*100,2),
        'perfomansi_percent' => number_format($x['perfomansi_90h']*100,2),
        'perfomansi_gap' => number_format($x['perfomansi_90h_gap']*100,2),
        'perfomansi_nominal' => number_format($x['perfomansi_90h_gap'] * $row['billing_3']),
        'target' => $target,
      );
      $area['perfomansi_target'][] = $target;
      $area['perfomansi_percent'][] = $x['perfomansi_90h'];//actual
      $area['perfomansi_gap'][] = $x['perfomansi_90h_gap'];
      $area['perfomansi_nominal'][] = $x['perfomansi_90h_gap'] * $row['billing_3'];
    }

    if(isset($row)){
      $temp[] = array(
        'regional' => 'AREA I',
        'kpi' => '90h',
        'area' => 'AREA I',
        'bill_cycle' => NULL,
        'billing' => (float) $row['billing_3'],
        'bucket' => (float) $row['bucket_3'],
        'collection' => 0,
        'perfomansi' => 0,
        'perfomansi_target' => number_format(array_sum($area['perfomansi_target']) / count($area['perfomansi_target'])*100,2),
        'perfomansi_percent' => number_format(array_sum($area['perfomansi_percent']) / count($area['perfomansi_percent'])*100,2),
        'perfomansi_gap' => number_format(array_sum($area['perfomansi_gap']) / count($area['perfomansi_gap'])*100,2),
        'perfomansi_nominal' => array_sum($area['perfomansi_nominal']),
        'target' => 0,
      );
    }


    //dd($temp);
    $bilco = datatables()->of($temp);
    //dd($bilco->toJson());
    return $bilco->toJson();
  }

    public function dashboardApiTarget(Request $request)
    {
        ini_set('precision', 15);
        $bc = false;
        if($request->has('bc')){
            $bc = true;
        }
        $d = new DateTime($request->get('end'));
        //dd($d);
        $d90h = BillingCollectionPoc::selectRaw('
                                        "REGIONAL" AS LABEL,
                                        CONCAT(bc.regional) AS regional,
                                        area,
                                        bc.regional AS subarea,
                                        bc.bill_cycle,
                                        sum( bc.bill_amount_2 ) AS billing_2,
                                        sum( bc.bill_amount_3 ) AS billing_3,
                                        sum( bc.bucket_2 ) AS bucket_2,
                                        sum( bc.bucket_3 ) AS bucket_3,
                                        billing_collections_targets.target as target  
                                    ')
            ->from('billing_collections_poc', 'bc')
            ->leftjoin('billing_collections_targets', 'bc.regional', '=', 'billing_collections_targets.regional')
            ->orderBy('billing_2', 'DESC')
            ->groupBy('bc.regional')
            ->where('billing_collections_targets.periode', '=', $d->format('Y-m-1'))
            ->where('bc.periode', '=', $request->get('end'))
            ->where('bc.regional', '!=', '**************')
            ->where('bc.area', '=', 'AREA I')
            ->where('bc.customer_type', '=', 'S');
            $targetArea = BillingCollectionTarget::select('target')->where('periode',$d->format('Y-m-1'))->where('regional','AREA1')->get()->toArray()[0]['target'];
        if($bc){
            if($request->has('bc_val')){
                $d90h->where('bc.bill_cycle','=',$request->get('bc_val'));
            }
        }
        $d90harr = $d90h->get()->toArray();
        $temp = array();
        $target = (float)0.974820372699075;
        $c = array(
            'comma'=> 2,
            'billing'=>0,
            'bucket'=>0,
            'collection'=>0,
            'perfomansi_nominal'=> 0
            );
        $area = array();
        foreach ($d90harr as $row) {
            $target = (float)$row['target'];
            $x = array();
            $x['collection_60h'] = $row['billing_2'] - $row['bucket_2'];
            $x['perfomansi_60h'] = $x['collection_60h'] / $row['billing_2'];
            $x['perfomansi_60h_gap'] = $target - $x['perfomansi_60h'];
            $x['collection_90h'] = $row['billing_3'] - $row['bucket_3'];
            $x['perfomansi_90h'] = $x['collection_90h'] / $row['billing_3'];
            $x['perfomansi_90h_gap'] = $target - $x['perfomansi_90h'];
            $x['perfomansi_nominal'] =$x['perfomansi_90h_gap'] * $row['billing_3'];
            //number_format($x['perfomansi_90h_gap'] * $row['billing_3'],false,'.', '.');
            $c['billing'] += (float)$row['billing_3'];
            $c['bucket'] += (float)$row['bucket_3'];
            $c['collection'] += (float)$x['collection_90h'];
            $c['perfomansi_nominal'] += $x['perfomansi_nominal'];

            $temp[] = array(
                'regional' => $row['regional'],
                'kpi' => '90h',
                'area' => $row['area'],
                'bill_cycle' => $row['bill_cycle'],
                'billing' => (float)$row['billing_3'],
                'bucket' => (float)$row['bucket_3'],
                'collection' => $x['collection_90h'],
                'perfomansi' => $x['perfomansi_90h'],
                'perfomansi_target' => (float) number_format($target * 100, $c['comma']),
                'perfomansi_percent' => (float)number_format($x['perfomansi_90h'] * 100, $c['comma']),
                'perfomansi_gap' => (float)number_format($x['perfomansi_90h_gap'] * 100, $c['comma'],'.', ''),
                'perfomansi_nominal' => number_format($x['perfomansi_nominal']),
                'target' => $target,
            );

            $area['perfomansi_target'][] = $target;
            $area['perfomansi_percent'][] = $x['perfomansi_90h'];//actual
            $area['perfomansi_gap'][] = $x['perfomansi_90h_gap'];
            $area['perfomansi_nominal'][] = $x['perfomansi_90h_gap'] * $row['billing_3'];
        }
        $region_sort = array_column($temp, 'regional');
        array_multisort($region_sort, SORT_DESC, $temp);

        if (isset($temp)) {
            $bill_3 = 0; $bucket_3 = 0;
            if(isset($row)){
                if(array_key_exists('billing_3',$row)){
                    $bill_3 = (float)$row['billing_3'];
                }
                if(array_key_exists('bucket_3', $row)){
                    $bucket_3 = (float)$row['bucket_3'];
                }
            }

            try{$m['perfomansi_percent'] = $c['collection'] / $c['billing'];}
            catch (\Exception $e){AppHelper::sendErrorAndExit('Data target not available for date: '. $request->get('end'));}
            $m['perfomansi_percent'] = $c['collection'] / $c['billing'];
            $m['perfomansi_target'] = $targetArea;
            $m['perfomansi_gap'] = $m['perfomansi_target'] - $m['perfomansi_percent'];
            $m['perfomansi_nominal']= $m['perfomansi_gap'] * $c['billing'];
            $temp[] = array(
                'regional' => 'AREA I',
                'kpi' => '90h',
                'area' => 'AREA I',
                'bill_cycle' => NULL,
                'billing' => $c['billing'],
                'bucket' => $c['bucket'],
                'collection' => $c['collection'],
                'perfomansi' => 0,
                'perfomansi_target' => number_format($m['perfomansi_target'] * 100, $c['comma']),
                'perfomansi_percent' => number_format(($m['perfomansi_percent'])* 100 , $c['comma']),
                'perfomansi_gap' => number_format($m['perfomansi_gap'] * 100, $c['comma']),
                'perfomansi_nominal' => number_format(array_sum($area['perfomansi_nominal'])),
                'target' => $m['perfomansi_target'],
            );
        }


        //dd($temp);
        $bilco = datatables()->of($temp);
        //dd($bilco->toJson());
        return $bilco->toJson();
    }

    public function dashboardApiCompare(Request $request){
        ini_set('xdebug.var_display_max_depth', '10');
        ini_set('xdebug.var_display_max_children', '256');
        ini_set('xdebug.var_display_max_data', '1024');
        DB::connection()->enableQueryLog();


        $bc = false;
        if($request->has('bc')){
            $bc = true;
        }


        DB::statement(DB::raw('SET @rankarea60h = 0;'));
        DB::statement(DB::raw('SET @rankarea90h = 0;'));

        $d60h = BillingCollectionPoc::selectRaw('
            *,billing_1 - billing_2 AS selisih')
            ->fromSub(function ($query) use($request,$bc) {
                $query->selectRaw('*')
                    ->fromSub(function ($query) use($request,$bc) {
                        $query->selectRaw('*,@rankarea60h := @rankarea60h + 1 AS rank60h')
                            ->fromSub(function ($query) use($request,$bc) {
                                $query->selectRaw('*,@rankarea90h := @rankarea90h + 1 AS rank90h')
                                    ->fromSub(function ($query) use($request,$bc) {
                                        $group = array();
                                        if($bc){
                                            $group[] = 'bill_cycle';
                                        }
                                        $group[] = 'bc.area';
                                        $query->selectRaw('
                                        "AREA" AS LABEL,
                                        IF(area NOT IN ("AREA I","AREA II","AREA III","AREA IV"), "NON AREA", area) as regional,
                                        area,
                                        regional AS subarea,
                                        bc.bill_cycle,
                                        ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
                                        ( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2   
                                    ')
                                            ->from('billing_collections_poc','bc')
                                            ->orderBy('billing_2','DESC')
                                            ->groupBy( $group)
                                            ->where('bc.periode','=' ,$request->get('start'))
                                            ->where('bc.customer_type','=','S')
                                            ->whereIn('bc.area', ['AREA I', 'AREA II', 'AREA III', 'AREA IV']);
                                        if($bc){
                                            if($request->has('bc_val')){
                                                $query->where('bill_cycle','=',$request->get('bc_val'));
                                            }
                                        }
                                    }, 'sub');
                            },'order_billing_2')
                            ->orderBy('billing_1','DESC');
                    },'order_biling_1')->orderBy('area');
            },'sub1');

        DB::statement(DB::raw('SET @rankregional60h = 0;'));
        DB::statement(DB::raw('SET @rankregional90h = 0;'));
        $d90h = BillingCollectionPoc::selectRaw('
            *,billing_1 - billing_2 AS selisih')
            ->fromSub(function ($query) use($request,$bc) {
                $query->selectRaw('*')
                    ->fromSub(function ($query) use($request,$bc) {
                        $query->selectRaw('*,@rankregional60h := @rankregional60h + 1 AS rank60h')
                            ->fromSub(function ($query) use($request,$bc) {

                                $query->selectRaw('*, @rankregional90h := @rankregional90h + 1 AS rank90h')
                                    ->fromSub(function ($query) use($request,$bc) {
                                        $group = array();
                                        if($bc){
                                            $group[] = 'bill_cycle';
                                        }
                                        $group[] = 'bc.regional';
                                        $query->selectRaw('
                                        "REGIONAL" AS LABEL,
                                        CONCAT("-- " ,bc.regional) AS regional,
                                        area,
                                        regional AS subarea,
                                        bc.bill_cycle,
                                        ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
                                        ( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2   
                                    ')
                                            ->from('billing_collections_poc','bc')
                                            ->orderBy('billing_2','DESC')
                                            ->groupBy( $group)
                                            ->where('bc.periode','=' ,$request->get('start'))
                                            ->where('bc.regional','!=' ,'**************')
                                            ->where('bc.customer_type','=','S');
                                    }, 'sub');
                                if($bc){
                                    if($request->has('bc_val')){
                                        $query->where('bill_cycle','=',$request->get('bc_val'));
                                    }
                                }
                            },'order_billing_2')
                            ->orderBy('billing_1','DESC');
                    },'order_biling_1');
            },'sub1');

        DB::statement(DB::raw('SET @rankarea60h = 0;'));
        DB::statement(DB::raw('SET @rankarea90h = 0;'));
        $d60h2 = BillingCollectionPoc::selectRaw('
            *,billing_1 - billing_2 AS selisih')
            ->fromSub(function ($query) use($request,$bc) {
                $query->selectRaw('*')
                    ->fromSub(function ($query) use($request,$bc) {
                        $query->selectRaw('*,@rankarea60h := @rankarea60h + 1 AS rank60h')
                            ->fromSub(function ($query) use($request,$bc) {
                                $query->selectRaw('*,@rankarea90h := @rankarea90h + 1 AS rank90h')
                                    ->fromSub(function ($query) use($request,$bc) {
                                        if($bc){
                                            $group[] = 'bill_cycle';
                                        }
                                        $group[] = 'bc.area';
                                        $query->selectRaw('
                                        "AREA" AS LABEL,
                                        IF(area NOT IN ("AREA I","AREA II","AREA III","AREA IV"), "NON AREA", area) as regional,
                                        area,
                                        regional AS subarea,
                                        bc.bill_cycle,
                                        ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
                                        ( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2   
                                    ')
                                            ->from('billing_collections_poc','bc')
                                            ->orderBy('billing_2','DESC')
                                            ->groupBy( $group)
                                            ->where('bc.periode','=' ,$request->get('end'))
                                            ->where('bc.customer_type','=','S')
                                        ->whereIn('bc.area', ['AREA I', 'AREA II', 'AREA III', 'AREA IV']);
                                        if($bc){
                                            if($request->has('bc_val')){
                                                $query->where('bill_cycle','=',$request->get('bc_val'));
                                            }
                                        }
                                    }, 'sub');
                            },'order_billing_2')
                            ->orderBy('billing_1','DESC');
                    },'order_biling_1')->orderBy('area');
            },'sub1');
        DB::statement(DB::raw('SET @rankregional60h = 0;'));
        DB::statement(DB::raw('SET @rankregional90h = 0;'));
        $d90h2 = BillingCollectionPoc::selectRaw('
            *,billing_1 - billing_2 AS selisih')
            ->fromSub(function ($query) use($request,$bc) {
                $query->selectRaw('*')
                    ->fromSub(function ($query) use($request, $bc) {
                        $query->selectRaw('*,@rankregional60h := @rankregional60h + 1 AS rank60h')
                            ->fromSub(function ($query) use($request, $bc) {
                                $query->selectRaw('*, @rankregional90h := @rankregional90h + 1 AS rank90h')
                                    ->fromSub(function ($query) use($request, $bc) {
                                        if($bc){
                                            $group[] = 'bill_cycle';
                                        }
                                        $group[] = 'bc.regional';
                                        $query->selectRaw('
                                        "REGIONAL" AS LABEL,
                                        CONCAT("-- " ,bc.regional) AS regional,
                                        area,
                                        regional AS subarea,
                                        bc.bill_cycle,
                                        ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
                                        ( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2   
                                    ')
                                            ->from('billing_collections_poc','bc')
                                            ->orderBy('billing_2','DESC')
                                            ->groupBy( $group)
                                            ->where('bc.periode','=' ,$request->get('end'))
                                            ->where('bc.regional','!=' ,'**************')
                                            ->where('bc.customer_type','=','S');
                                        if($bc){
                                            if($request->has('bc_val')){
                                                $query->where('bill_cycle','=',$request->get('bc_val'));
                                            }
                                        }
                                    }, 'sub');
                            },'order_billing_2')
                            ->orderBy('billing_1','DESC');
                    },'order_biling_1');
            },'sub1');




        $d60h->union($d90h);
        $d60h2->union($d90h2);
        $temp = array();
        $finaltemp = array();
        $d60h2arr = $d60h2->get()->toArray();
        //var_dump($d60h2arr);
        //die();
        //die( var_dump(DB::getQueryLog($d90h2)[8]));
        //dd($d60h2arr);
        //die( DB::getQueryLog($d90h2)[4]['query']);
        $d60harr = $d60h->get()->toArray();
        //echo Str::replaceArray('?', $d90h->getBindings(), $d90h->toSql());
        //dd($d90h->get()->toArray());
        foreach ($d60harr as $row){
            if($bc){
                if($row['billing_1'] != null OR $row['billing_2'] != null){
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['bill_cycle'] = $row['bill_cycle'];
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['regional'] = $row['regional'] ;
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_1'] = $temp[$row['area']][$row['regional']]['billing_2']= null;
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_1'] = (float) number_format(($row['billing_1'] * 100),2);
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_2'] = (float) number_format(($row['billing_2'] * 100),2);
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['rank_60h'] =  $row['rank60h'];
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['rank_90h'] =  $row['rank90h'];
                }

            }else{
                $temp[$row['area']][$row['regional']]['regional'] = $row['regional'] ;
                $temp[$row['area']][$row['regional']]['billing_1'] = $temp[$row['area']][$row['regional']]['billing_2']= null;
                $temp[$row['area']][$row['regional']]['billing_1'] = (float) number_format(($row['billing_1'] * 100),2);
                $temp[$row['area']][$row['regional']]['billing_2'] = (float) number_format(($row['billing_2'] * 100),2);
                $temp[$row['area']][$row['regional']]['rank_60h'] =  $row['rank60h'];
                $temp[$row['area']][$row['regional']]['rank_90h'] =  $row['rank90h'];
            }

        }


        foreach ($d60h2arr as $row){
            if($bc){
                if($row['billing_1'] != null OR $row['billing_2'] != null) {
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['bill_cycle'] = $row['bill_cycle'];
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['regional'] = $row['regional'];
                    //$temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_1_1'] = $temp[$row['area']][$row['regional']]['billing_2_1'] = null;
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_1_1'] = (float)number_format(($row['billing_1'] * 100), 2);
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['billing_2_1'] = (float)number_format(($row['billing_2'] * 100), 2);
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['rank_60h'] = $row['rank60h'];
                    $temp[$row['bill_cycle']][$row['area']][$row['regional']]['rank_90h'] = $row['rank90h'];
                }
            }else{
                $temp[$row['area']][$row['regional']]['regional'] =  $row['regional'] ;
                $temp[$row['area']][$row['regional']]['billing_1_1'] = $temp[$row['area']][$row['regional']]['billing_2_1']= null;
                $temp[$row['area']][$row['regional']]['billing_1_1'] = (float) number_format(($row['billing_1'] * 100),2);
                $temp[$row['area']][$row['regional']]['billing_2_1'] = (float) number_format(($row['billing_2'] * 100),2);
                $temp[$row['area']][$row['regional']]['rank_60h'] =  $row['rank60h'];
                $temp[$row['area']][$row['regional']]['rank_90h'] =  $row['rank90h'];
            }

        }

        if($bc){
            foreach ($temp as $key => $value) {
                if (!is_int($key)) {
                    unset($temp[$key]);
                }
            }
        }
        if($bc){
            foreach ($temp as $bc) {
                foreach ($bc as $area => $data) {
                    foreach ($data as $row) {
                        $temp[$row['bill_cycle']][$area][$row['regional']]['selisih1'] = $temp[$area][$row['regional']]['selisih2'] = null;
                        if (array_key_exists('billing_1', $row) AND array_key_exists('billing_1_1', $row)) {
                            $temp[$row['bill_cycle']][$area][$row['regional']]['selisih1'] = (float)number_format($row['billing_1_1'] - $row['billing_1'], 2);
                        }
                        if (array_key_exists('billing_2', $row) AND array_key_exists('billing_2_1', $row)) {
                            $temp[$row['bill_cycle']][$area][$row['regional']]['selisih2'] = (float)number_format($row['billing_2_1'] - $row['billing_2'], 2);
                        }

                        if (!(array_key_exists('billing_1', $row))) $temp[$row['bill_cycle']][$area][$row['regional']]['billing_1'] = null;
                        if (!(array_key_exists('billing_2', $row))) $temp[$row['bill_cycle']][$area][$row['regional']]['billing_2'] = null;
                        if (!(array_key_exists('billing_1_1', $row))) $temp[$row['bill_cycle']][$area][$row['regional']]['billing_1_1'] = null;
                        if (!(array_key_exists('billing_2_1', $row))) $temp[$row['bill_cycle']][$area][$row['regional']]['billing_2_1'] = null;
                    }
                }
            }

        }else{
            foreach ($temp as $area => $data){
                foreach ($data as $row){
                    $temp[$area][$row['regional']]['selisih1'] = $temp[$area][$row['regional']]['selisih2'] = null;
                    if(array_key_exists('billing_1',$row) AND  array_key_exists('billing_1_1',$row))
                    {
                        $temp[$area][$row['regional']]['selisih1'] = (float) number_format($row['billing_1_1'] - $row['billing_1'] ,2);
                    }
                    if(array_key_exists('billing_2',$row) AND  array_key_exists('billing_2_1',$row))
                    {
                        $temp[$area][$row['regional']]['selisih2'] = (float) number_format($row['billing_2_1'] - $row['billing_2'],2);
                    }

                    if(!(array_key_exists('billing_1',$row))) $temp[$area][$row['regional']]['billing_1'] = null;
                    if(!(array_key_exists('billing_2',$row))) $temp[$area][$row['regional']]['billing_2'] = null;
                    if(!(array_key_exists('billing_1_1',$row))) $temp[$area][$row['regional']]['billing_1_1'] = null;
                    if(!(array_key_exists('billing_2_1',$row))) $temp[$area][$row['regional']]['billing_2_1'] = null;
                }
            }
        }

        //sorting php7
        $tempx = [];
        $areaord = [1 => 'Sumbagut', 2 => 'Sumbagteng', 3 => 'Sumbagsel','Jabotabek','Bali Nusra','Jawa Barat','Jawa Tengah','Jawa Timur','Kalimantan','Puma','Sulawesi'];
        foreach ($temp as $key => $val){
            foreach ($val as $v => $k){
                $val[$v]['order'] = 0;
                if(array_search(substr($v, 3),$areaord) > 0){
                    $val[$v]['order'] = array_search(substr($v, 3),$areaord);
                }
            }
            usort($val, function($a, $b) {
                return $a['order'] <=> $b['order']  ;
            });
            $tempx[$key] = $val;
        }
        $temp = $tempx;


        if($bc){
            foreach ($temp as $key => $value) {
                if (!is_int($key)) {
                    unset($temp[$key]);
                }
            }
            foreach ($temp as $r => $x){
                foreach ($x as $y){
                    foreach ($y as $z){
                        $finaltemp[] = $z;
                    }

                }
            }
        }else{
            foreach ($temp as $r => $x){
                foreach ($x as $y){
                    $finaltemp[] = $y;
                }
            }
        }


        //dd($temp);

        $bilco = datatables()->of($finaltemp);
        return $bilco->toJson();
    }

    public function dashboardApiPOC(){
        $bilco = BillingCollection::groupBy('bill_cycle', 'poc')
            ->selectRaw('
                                    area,
                                    bill_cycle,
                                    poc,
                                    sum(bill_amount_2) as 60h, 
                                    sum(bill_amount_3) as 90h,
                                    sum(`bill_amount_2`) - sum(`bucket_2`) as \'60hCOL\',
                                    SUM(`bill_amount_3`) - sum(`bucket_3`) as \'90hCOL\',
                                    (SUM(`bill_amount_2`) - sum(`bucket_2`)) / SUM(`bill_amount_2`) as \'60hCOLPERCENT\',
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / SUM(`bill_amount_3`) as \'90hCOLPERCENT\'
                                    ')
            ->get();
        $result = json_decode((string) $bilco, true);
        return response()->json($result, $this->successStatus);
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($name, Request $request =null,$notify = 0)
  {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');
    $x = 1;


    $importer = Importer::create(array(
      'importedRow'=>0,
      'storedRow'=>0,
      'status' => 'QUEUE',
        'tipe' => 'bilcollection:import',
        'filename' => $name
    ));

    /* if($request->has('judul')){
         $x = 0;
     }*/
    $lexer = new Lexer(new LexerConfig());
    $interpreter = new Interpreter();
    $j = $i = 0;
    $arr= array();
    $arr1= array();

    $interpreter->addObserver(function(array $row) use (&$i,&$j,&$x,&$arr1,$importer) {

      $lokal = array();
      $periode = $row[0];
      $i += 1;
      $j += 1;
      if ($i === $x) {
        return;
      }
      $poc =($row[5] === 'poc' ? '***' : ($row['5']));
      $bill_cycle = ($row[6] === 'bill_cycle' ? 0 : (int)($row['6']));


      if(isset($lokal[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_1'])){
      }else {

        /*if($bill_cycle === 1 AND strtolower($poc) === 'jakarta'){*/
        for ($bc = 1; $bc <= 12; $bc++) {
          $lokal[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_'.$bc] = $row[10+$bc-1];
        }
        for ($bc = 1; $bc <= 13; $bc++) {
          $lokal[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bucket_'.$bc] = $row[22+$bc-1];
        }
        /*}*/

      }

      /*if ($bill_cycle === 1 AND strtolower($poc) === 'jakarta') {*/
      if(!(isset($arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]))){
        for ($bc = 1; $bc <= 12; $bc++) {
          $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_'.$bc] = $row[10+$bc-1];
        }
        for ($bc = 1; $bc <= 13; $bc++) {
          $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bucket_'.$bc] = $row[22+$bc-1];
        }
        $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['total_trans'] = 0;
      }else{
        for ($bc = 1; $bc <= 12; $bc++) {
          $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_'.$bc] = $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_'.$bc] + $row[10+$bc-1];
        }
        for ($bc = 1; $bc <= 13; $bc++) {
          $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bucket_'.$bc] = $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bucket_'.$bc] + $row[22+$bc-1];
        }
      }

      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['total_trans'] = 1 + $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['total_trans'];
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['blocking_status'] =$row[8];
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['customer_type'] =$row[7];
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_cycle'] =$bill_cycle;
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['poc'] =$poc;
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['regional'] =$row[4];
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['area'] =$row[3];
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['periode'] =$periode;
      $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['import_batch'] = $importer->id;
      $bill_amount_n_v = array();
      /*for ($l = 1; $l <= 12; $l++) {
          $bill_amount_n_v['bill_amount_' . $l] = $arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]]['bill_amount_' . $l] + (int)$row[10 - 1 - 1];
      }*/
      //$arr1[$periode][$bill_cycle][$poc][$row[7]][$row[8]] = array('bill_amount' => $bill_amount_n_v);
      /*}*/

      //$x = $arr;

//$final = $arr1;
      //if($j >= 1000){

      $final = $arr1;


      /* $arr = collect($arr);
       $chunks = $arr->chunk(500);
       foreach ($chunks as $chunk)
       {
           //BillingCollection::insert($chunk->toArray());
       }*/
      //$j = 0;
      //}
      //BillingCollection::create($arr);
    });
    //$lexer->parse($request->file('file'), $interpreter);

    $lexer->parse(Storage::disk()->path('/bilcollection/csv/'.$name), $interpreter);
    if (ob_get_level()) ob_end_clean();
    $finale = array();
    foreach ($arr1 as $periodes => $bcs){
      foreach ($bcs as $bc => $pocs)
      {
        foreach ($pocs as $poc => $customer_types) {
          foreach ($customer_types as $customer_type => $blocking_statuses ){
            foreach ($blocking_statuses as $row){
              $finale[] = $row;
            }
          }
        }
      }
    }

    $finale = collect($finale);
    $chunks =$finale->chunk(500);
    foreach ($chunks as $chunk)
    {
      BillingCollectionPoc::insert($chunk->toArray());
    }
    $importer->importedRow = $finale->count();
    $importer->storedRow = $finale->count();
    $importer->status = "Finish";
    $importer->save();
    if($notify == 1){
        $user = Notifier::create([
            'type' => 'CollectionImport',
            'subject' => 'Collection Import file',
            'message' => $name . ' has been recorded',
        ]);
    }
    die();
    var_dump($arr1);
    var_dump($arr);

    echo $i;
    die();
    $arr = collect($arr);
    $chunks = $arr->chunk(500*2);

    foreach ($chunks as $chunk)
    {
      BillingCollection::insert($chunk->toArray());
    }
    //

    echo count($arr);
    //var_dump($arr);
    die();
    //
    error_reporting(E_ALL);


    ini_set('display_errors', 'on');
    $file = fopen($request->file('file'),'r');
    $header = null;
    $data = array();

    $importer = Importer::create(array(
      'importedRow'=>0,
      'storedRow'=>0,
      'status' => 'QUEUE'
    ));

    $stored = $i = 0;
    if ($file) {
      while ($row = fgetcsv($file,10000, ",")) {
        //if($i > 0) var_dump($row);
        if($i>1){
          $arr = array(
            'import_batch' => $importer->id,
            'periode'     => $row[0],
            'account_number'    => $row[1],
            'msisdn' =>  substr($row[2], 0, -3) . '***',
            'area' => $row[3],
            'regional' => $row[4],
            'poc' => $row[5],
            'bill_cycle' => $row[6],
            'customer_type' => $row[7],
            'blocking_status' => $row[8],
            'rt' => $row[9],
            'bill_amount_2' => (int)  $row[12],
            'bill_amount_3' => (int)  $row[13],
            'bucket_2' => (int)  $row[23],
            'bucket_3' => (int)  $row[24],
            'rec'   => 1
          );

          if($arr['customer_type'] == 'S'){
            $bilco = BillingCollection::create($arr);
            if($bilco){
              $stored++;
            }

          }


        }
        $i++;
      }
    }
    /* $customerArr = $this->csvToArray($file);
     for ($i = 0; $i < count($customerArr); $i ++)
     {
         var_dump($customerArr);
         //User::firstOrCreate($customerArr[$i]);
     }
*/

    $importer->importedRow = $i;
    $importer->storedRow = $stored;
    $importer->storedRow = $stored;
    $importer->status = "Finish";
    $importer->save();
    return 'Jobi done or what ever TOTAL ROW:'.$i;

    //return back();
  }


  /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create2($name, Request $request =null)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
        ini_set('xdebug.var_display_max_depth', '10');
        ini_set('xdebug.var_display_max_children', '256');
        ini_set('xdebug.var_display_max_data', '1024');

      ini_set('display_errors', 'on');
      $file = fopen(Storage::disk()->path('/bilcollection/csv'.$name),'r');
      $header = null;
      $data = array();

      $importer = Importer::create(array(
        'importedRow'=>0,
        'storedRow'=>0,
        'status' => 'QUEUE'
      ));

      $stored = $i = 0;
      if ($file) {
        while ($row = fgetcsv($file,10000, ",")) {
          //if($i > 0) var_dump($row);
          if($i>1){
            $arr = array(
              'import_batch' => $importer->id,
              'periode'     => $row[0],
              'account_number'    => $row[1],
              'msisdn' =>  substr($row[2], 0, -3) . '***',
              'area' => $row[3],
              'regional' => $row[4],
              'poc' => $row[5],
              'bill_cycle' => $row[6],
              'customer_type' => $row[7],
              'blocking_status' => $row[8],
              'rt' => $row[9],
              'bill_amount_1' => (int)  $row[10],
              'bill_amount_2' => (int)  $row[11],
              'bill_amount_3' => (int)  $row[12],
              'bill_amount_4' => (int)  $row[13],
              'bill_amount_5' => (int)  $row[14],
              'bill_amount_6' => (int)  $row[15],
              'bill_amount_7' => (int)  $row[16],
              'bill_amount_8' => (int)  $row[17],
              'bill_amount_9' => (int)  $row[18],
              'bill_amount_10' => (int)  $row[19],
              'bill_amount_11' => (int)  $row[20],
              'bill_amount_12' => (int)  $row[21],
              'bucket_1' => (int)  $row[22],
              'bucket_2' => (int)  $row[23],
              'bucket_3' => (int)  $row[24],
              'bucket_4' => (int)  $row[25],
              'bucket_5' => (int)  $row[26],
              'bucket_6' => (int)  $row[27],
              'bucket_7' => (int)  $row[28],
              'bucket_8' => (int)  $row[29],
              'bucket_9' => (int)  $row[30],
              'bucket_10' => (int)  $row[31],
              'bucket_11' => (int)  $row[32],
              'bucket_12' => (int)  $row[33],
              'bucket_13' => (int)  $row[34],
              'rec'   => 1
            );

            if($arr['customer_type'] == 'S'){
              $bilco[] = $arr;
                $stored++;
            }

          }
          $i++;
        }
      }

      $arr = collect($bilco);
      $chunks = $arr->chunk(500);

      foreach ($chunks as $chunk)
      {
        BillingCollection::insert($chunk->toArray());
      }
      $importer->importedRow = $i;
      $importer->storedRow = $stored;
      $importer->storedRow = $stored;
      $importer->status = "Finish";
      $importer->save();
      return 'Jobi done or what ever TOTAL ROW:'.$i;

      //return back();

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
     * @param  \App\BillingCollection  $billingCollection
     * @return \Illuminate\Http\Response
     */
    public function show(BillingCollection $billingCollection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BillingCollection  $billingCollection
     * @return \Illuminate\Http\Response
     */
    public function edit(BillingCollection $billingCollection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BillingCollection  $billingCollection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BillingCollection $billingCollection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BillingCollection  $billingCollection
     * @return \Illuminate\Http\Response
     */
    public function destroy(BillingCollection $billingCollection)
    {
        //
    }
}
