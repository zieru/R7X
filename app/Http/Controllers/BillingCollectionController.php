<?php

namespace App\Http\Controllers;

use App\BillingCollection;
use App\Importer;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

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
    public function dashboardApi(){
        $bilco = BillingCollection::groupBy('bill_cycle')
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
    public function create(Request $request)
    {
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
