<?php

namespace App\Http\Controllers\API;
use App\FormAdjustment;
use App\Helpers\AppHelper;
use Carbon\Carbon;
use App\Imports\FormAdjustmentImport;
use App\Importer;
use App\FormRefund;

use Auth;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Response;

class FormRefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $grup = 'user';

        try {Carbon::createFromFormat('Y-m', $request->get('period'));}
        catch (\Exception $e){AppHelper::sendErrorAndExit('Periode is invalid');}

        $period = Carbon::createFromFormat('Y-m-d', $request->get('period')."-01");
        if($request->has('grup')){
            $grup = $request->get('grup');
        }
        switch(strtoupper($grup)){
            case 'USER':
                $tbl_grup = 'user_eksekutor';
                break;
            case 'REASON':
                $tbl_grup = 'reason';
                break;
        }
        $data = [];
        $f= FormRefund::groupBy($tbl_grup)
            ->selectRaw("$tbl_grup,count(msisdn) as msisdn,format(sum(new_balance) - sum(balance),0) as nominal")
            ->whereBetween('tanggal_eksekusi',[$period->format('Y-m-1'),$period->format('Y-m-t')]);

        $x= FormRefund::selectRaw('user_eksekutor,reason, msisdn,format(new_balance - balance,0) as nominal')
            ->whereBetween('tanggal_eksekusi',[$period->format('Y-m-1'),$period->format('Y-m-t')]);
        $loop= 0;
        foreach ($f->get()->toArray() as $head){
            $data[$loop] = $head;
            foreach ($x->get()->toArray() as $child){
                if($child[$tbl_grup] === $head[$tbl_grup]) $data[$loop]['children'][] = $child;
            }
            $loop++;
        }

        //dd($data);
        return datatables()->of($data)->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     // header("Access-Control-Allow-Origin: *");

      $importer = Importer::create(array(
        'importedRow'=>0,
        'storedRow'=>0,
        'status' => 'QUEUE'
      ));
        $rowx = array();
     $excel = Excel::toArray(new FormAdjustmentImport, $request->file('files'));
     foreach ($excel[0] as $row){
         if($row['shop'] !== null) {
             $d = array();
             $now = Carbon::now('utc')->toDateTimeString();
             $row['import_batch'] = $importer->id;
             $d['shop'] = $row['shop'];
             $d['import_batch'] = $row['import_batch'];
             $d['author'] = $row['author'] = Auth::user()->id;
             $d['msisdn'] = $row['msisdn'];
             $d['amount'] = $row['amount'];
             $d['balance'] = $row['balance'];
             $d['new_balance'] = $row['new_balance'];
             $d['reason'] = $row['reason'];
             $d['nodin_ba'] = $row['nodin_ba'];
             $d['notes_dsc'] = $row['notes_dsc'];
             $d['user_eksekutor'] = $row['user_eksekutor'];
             $tgl_eksekusi = $tgl_permintaan = 'empty';

             $d['created_at'] = $row['created_at'] = $now;
             $d['updated_at'] = $row['updated_at'] = $now;


             if(!isset($row['tgl_permintaan'])){
                 AppHelper::sendErrorAndExit('Please check column tgl_permintaan format',500);
             }
             if(!isset($row['tgl_eksekusi'])){
                 AppHelper::sendErrorAndExit('Please check column tgl_eksekusi format',500);
             }
             try {
                 gmdate("Y-m-d", ($row['tgl_permintaan'] - 25569) * 86400);
             }
             catch (Exception $e) {
                 AppHelper::sendErrorAndExit($e->getMessage().' Please check column tgl_permintaan ('.$row['tgl_permintaan'].') format',500);
             }
             try{
                 gmdate("Y-m-d", ($row['tgl_eksekusi'] - 25569) * 86400);
             }
             catch (Exception $e) {
                 AppHelper::sendErrorAndExit($e->getMessage().' Please check column tgl_eksekusi ('.$row['tgl_eksekusi'].') format',500);
             }
             $d['tanggal_permintaan'] = $row['tanggal_permintaan'] = gmdate("Y-m-d", ($row['tgl_permintaan'] - 25569) * 86400);
             $d['tanggal_eksekusi'] = $row['tanggal_eksekusi'] = gmdate("Y-m-d", ($row['tgl_eksekusi'] - 25569) * 86400);

             unset($row['tgl_permintaan']);
             unset($row['tgl_eksekusi']);
             $rowx[] = $d;
         }
     }
      //dd($rowx);
      FormRefund::insert($rowx);
      $importer->status = "Finish";
      $importer->save();
      return Response::json(array('message' => 'Upload Success!'),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FormAdjustment  $formAdjustment
     * @return \Illuminate\Http\Response
     */
    public function show(FormRefund $FormRefund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FormAdjustment  $formAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormAdjustment $formAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FormAdjustment  $formAdjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormAdjustment $formAdjustment)
    {
        //
    }
}
