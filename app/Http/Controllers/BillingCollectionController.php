<?php

namespace App\Http\Controllers;

use App\BillingCollection;
use App\BillingCollectionPoc;
use App\Importer;
use DB;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
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

    public function compactPOC(){
        $periode = "2020-04-30";
        //DB::enableQueryLog() ;
        $d90h = BillingCollectionPoc::groupBy('bill_cycle','poc')
            ->selectRaw('
            periode,
            area,
            regional,
            poc,
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
            ->orderBy('periode')
            ->orderBy('poc');
        //echo 'x';
        //var_dump($d90h);
        //dd(DB::getQueryLog());
        foreach ($d90h->get()->toArray() as $d){
            BillingCollectionPoc::create($d);
        }
    }

    public function dashboardApi(){
        $d60h = BillingCollectionPOC::groupBy('periode','poc')
            ->selectRaw('periode,
                                poc as regional,
                                    sum(bill_amount_2) as billing , 
                                    sum(`bucket_2`) as osbalance,
                                    sum(`bill_amount_2`) - sum(`bucket_2`) as collection,
                                    "60h" as kpi,
                                    (SUM(`bill_amount_2`) - sum(`bucket_2`)) / SUM(`bill_amount_2`) as performansi
                                    ')
            ->orderBy('periode')
            ->orderBy('poc')
            ->having('billing','>',0);
        $d90h = BillingCollectionPoc::groupBy('periode','poc')
            ->selectRaw('periode,
                                poc as regional,
                                    sum(bill_amount_3) as billing , 
                                    sum(`bucket_3`) as osbalance,
                                    sum(`bill_amount_3`) - sum(`bucket_3`) as collection,
                                    "90h" as kpi,
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / SUM(`bill_amount_3`) as performansi
                                    ')
            ->orderBy('periode')
            ->orderBy('poc')
            ->having('billing','>',0)
            ->union($d60h);

        $bilco = datatables()->of($d90h);
        //$result = json_decode((string) $bilco, true);
        $result = $bilco;
        //return response()->json($result, $this->successStatus);
        return $bilco->toJson();
    }
    public function dashboardApiArea(){
        $d60h = BillingCollectionPOC::groupBy('periode','area')
            ->selectRaw('periode,
                            area,
                                    sum(bill_amount_2) as billing, 
                                    sum(`bucket_2`) as osbalance,
                                    sum(`bill_amount_2`) - sum(`bucket_2`) as collection,
                                    "60h" as kpi,
                                    (SUM(`bill_amount_2`) - sum(`bucket_2`)) / SUM(`bill_amount_2`) as performansi
                                    ')
            ->orderBy('periode')
            ->orderBy('area')
            ->having('billing','>',0);
        $d90h = BillingCollectionPOC::groupBy('periode','area')
            ->selectRaw('periode,
                                area,
                                    sum(bill_amount_3) as billing , 
                                    sum(`bucket_3`) as osbalance,
                                    sum(`bill_amount_3`) - sum(`bucket_3`) as collection,
                                    "90h" as kpi,
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / SUM(`bill_amount_3`) as performansi
                                    ')
            ->orderBy('periode')
            ->orderBy('area')
            ->having('billing','>',0)
            ->union($d60h);

        $bilco = datatables()->of($d90h);
        //$result = json_decode((string) $bilco, true);
        $result = $bilco;
        //return response()->json($result, $this->successStatus);
        return $bilco->toJson();
    }
    public function dashboardApiCompare(Request $request){

        $d60h = BillingCollectionPOC::groupBy('bc.bill_cycle', 'bc.area')
            ->selectRaw('
            "60H" AS LABEL,
            @row_num:= @row_num+1 AS ROWNUM,
bc.area as regional,
bc.area,
NULL as subarea,
bc.bill_cycle,
( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
	( Sum( bc2.bill_amount_2 ) - Sum( bc2.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1_1,
	( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2,
	( Sum( bc2.bill_amount_3 ) - Sum( bc2.bucket_3 ) ) / sum( bc.bill_amount_2 ) AS billing_2_1,
	( ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) ) - ( ( Sum( bc2.bill_amount_2 ) - Sum( bc2.bucket_2 ) ) / sum( bc2.bill_amount_2 ) ) AS selisih1,
	( ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) ) - ( ( Sum( bc2.bill_amount_3 ) - Sum( bc2.bucket_3 ) ) / sum( bc2.bill_amount_3 ) ) AS selisih2  
')
            ->from('billing_collections_poc','bc')
            ->LEFTJOIN('billing_collections_poc as bc2',function($q)use($request) /*use(null)*/
            {
                $q->on('bc2.poc', '=', 'bc.poc')
                    ->on('bc2.bill_cycle', '=', 'bc.bill_cycle')
                    ->where('bc2.periode','=', $request->get('end'));
            })

            ->orderBy('bc.bill_cycle','DESC')
            ->orderBy('bc.area','ASC')
            ->where('bc.periode','=' ,$request->get('start'));
        $d90h = BillingCollectionPoc::groupBy('bc.bill_cycle','bc.regional')
            ->selectRaw('
            "90H" AS LABEL,
            @row_num:= @row_num+1,
bc.regional as regional,
NULL as area,
bc.area as subarea,
bc.bill_cycle,
( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1,
	( Sum( bc2.bill_amount_2 ) - Sum( bc2.bucket_2 ) ) / sum( bc.bill_amount_2 ) AS billing_1_1,
	( Sum( bc.bill_amount_3 ) - Sum( bc.bucket_3 ) ) / sum( bc.bill_amount_3 ) AS billing_2,
	( Sum( bc2.bill_amount_3 ) - Sum( bc2.bucket_3 ) ) / sum( bc.bill_amount_2 ) AS billing_2_1,
	( ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) ) - ( ( Sum( bc2.bill_amount_2 ) - Sum( bc2.bucket_2 ) ) / sum( bc2.bill_amount_2 ) ) AS selisih1,
	( ( Sum( bc.bill_amount_2 ) - Sum( bc.bucket_2 ) ) / sum( bc.bill_amount_2 ) ) - ( ( Sum( bc2.bill_amount_3 ) - Sum( bc2.bucket_3 ) ) / sum( bc2.bill_amount_3 ) ) AS selisih2  
')
            ->from('billing_collections_poc','bc')
            ->LEFTJOIN('billing_collections_poc as bc2',function($q)use($request) /*use(null)*/
            {
                $q->on('bc2.poc', '=', 'bc.poc')
                    ->on('bc2.bill_cycle', '=', 'bc.bill_cycle')
                    ->where('bc2.periode','=', $request->get('end'));
            })

            ->orderBy('bc.bill_cycle','DESC')
            ->orderBy('bc.area','ASC')
            ->where('bc.periode','=' ,'2020-04-30')
            ->union($d60h);
        $bilco = datatables()->of($d90h);
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
    public function create($name, Request $request =null)
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
            'status' => 'QUEUE'
        ));

       /* if($request->has('judul')){
            $x = 0;
        }*/
        $lexer = new Lexer(new LexerConfig());
        $interpreter = new Interpreter();
        $j = $i = 0;
        $arr= array();
        $arr1= array();

        $final = array();
        $interpreter->addObserver(function(array $row) use (&$i,&$j,&$x,&$arr1,&$final,$importer) {

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


            if($j >= 1000){

                $final = $arr1;


               /* $arr = collect($arr);
                $chunks = $arr->chunk(500);
                foreach ($chunks as $chunk)
                {
                    //BillingCollection::insert($chunk->toArray());
                }*/
                $j = 0;
            }
            //BillingCollection::create($arr);
        });
        //$lexer->parse($request->file('file'), $interpreter);

        $lexer->parse(Storage::disk()->path($name), $interpreter);
        if (ob_get_level()) ob_end_clean();
        $finale = array();
        foreach ($final as $periodes => $bcs){
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
        $importer->status = "Finish";
        $importer->save();
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
