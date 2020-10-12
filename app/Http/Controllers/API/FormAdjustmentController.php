<?php

namespace App\Http\Controllers\API;
use App\Helpers\AppHelper;
use Carbon\Carbon;
use App\Imports\FormAdjustmentImport;
use App\Importer;
use DateTime;
use DB;
use Exception;
use Response;
use App\FormAdjustment;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FormAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function report(Request $request){
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

        //dd(FormAdjustment::all());
        $f= FormAdjustment::groupBy($tbl_grup)
            ->selectRaw("$tbl_grup,count(msisdn) as msisdn,FORMAT(sum(nominal),0) as nominal")
            ->whereBetween('tgl_adj',[$period->format('Y-m-1'),$period->format('Y-m-t')]);
        $x= FormAdjustment::selectRaw("user_eksekutor,reason, msisdn, FORMAT(nominal,0) as nominal, id")
            ->whereBetween('tgl_adj',[$period->format('Y-m-1'),$period->format('Y-m-t')]);
        $loop= 0;
       foreach ($f->get()->toArray() as $head){
           $data[$loop] = $head;
           foreach ($x->get()->toArray() as $child){
               if($child[$tbl_grup] === $head[$tbl_grup]) $data[$loop]['children'][] = $child;
           }
           $loop++;
       }

      return datatables()->of($data)->toJson();
    }
    public function reportreason(){
      $f=  FormAdjustment::groupBy('reason')
      ->select('reason', DB::raw('count(*) as total'));
      return datatables()->of($f)->toJson();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeold(Request $request)
    {
      //header('Access-Control-Allow-Origin: *');

      $importer = Importer::create(array(
        'importedRow'=>0,
        'storedRow'=>0,
        'status' => 'QUEUE'
      ));
      $FormAdjustmentImport = new FormAdjustmentImport();

      $excel = Excel::toArray($FormAdjustmentImport, $request->file('files'));
      $rowx = array();
     foreach ($excel[0] as $row){
         $now = Carbon::now('utc')->toDateTimeString();
         if($row['shop'] != null){
         $row['import_batch'] = $importer->id;
         $row['author'] = Auth::user()->id;
         try { gmdate("Y-m-d", ($row['bulan_tagihan'] - 25569) * 86400);}
         catch (Exception $e) { AppHelper::sendErrorAndExit($e->getMessage().' Please check column bulan_tagihan ('.$row['bulan_tagihan'].') format',500);}

         try{ gmdate("Y-m-d", ($row['tanggal_adjustment'] - 25569) * 86400);}
         catch (Exception $e) { AppHelper::sendErrorAndExit($e->getMessage().' Please check column tanggal_adjustment ('.$row['tanggal_adjustment'].') format',500);}

         $row['bulantagihan'] = gmdate("Y-m-d", ($row['bulan_tagihan'] - 25569) * 86400);
         $row['created_at'] = $now;
         $row['updated_at'] = $now;
         $row['tgl_adj'] = gmdate("Y-m-d", ($row['tanggal_adjustment'] - 25569) * 86400);
         $row['nodin_ba'] = $row['nomor_nodinba'];
         unset($row['bulan_tagihan']);unset($row['nomor_nodinba']);unset($row['tanggal_adjustment']);unset($row['']);
         $rowx[] = $row;
       }
     }
      FormAdjustment::insert($rowx);
      $importer->status = "Finish";
      $importer->save();
      return Response::json(array('message' => 'Upload Success!'),200);
    }
    public function store(Request $request)
    {

        $FormAdjustment = new FormAdjustment();
        $FormAdjustment->shop = $request->shop;
        $FormAdjustment->account = $request->account;
        $FormAdjustment->msisdn = $request->MSISDN;
        $FormAdjustment->bill_cycle = $request->bill_cycle;
        $FormAdjustment->status_msisdn = $request->statusmsisdn;
        $FormAdjustment->los = $request->los;
        $FormAdjustment->arpu = $request->arpu;
        $FormAdjustment->bulantagihan = $request->bulantagihan;
        $FormAdjustment->nominal = $request->nominal;
        $FormAdjustment->reason = $request->reason;
        $FormAdjustment->notes_dsc = $request->notes_dsc;
        $FormAdjustment->nodin_ba = $request->nodin_ba;
        $FormAdjustment->tgl_adj = $request->tgl_adj;
        $FormAdjustment->user_eksekutor = $request->user_eksekutor;
        $FormAdjustment->import_batch = 0;
        $FormAdjustment->author = Auth::user()->id;
        $saved = $FormAdjustment->save();
        if(!$saved){
            AppHelper::sendErrorAndExit('Error Input ERROR',500);
        }

        return Response::json(array('message' => 'Upload Success!'),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FormAdjustment  $formAdjustment
     * @return \Illuminate\Http\Response
     */
    public function show(FormAdjustment $FormAdjustment)
    {
        return datatables()->of($FormAdjustment)->toJson();
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
