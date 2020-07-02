<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use App\Imports\FormAdjustmentImport;
use App\Importer;
use DB;

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

    public function report(){
      $f=  FormAdjustment::with('User')->groupBy('author')
        ->selectRaw('author,count(msisdn) as msisdn,sum(nominal) as nominal');
      return datatables()->of($f)->toJson();
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
    public function store(Request $request)
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
       if($row['shop'] != null){
         $row['import_batch'] = $importer->id;
         $row['author'] = Auth::user()->id;
         $row['bulantagihan'] = gmdate("Y-m-d", ($row['bulan_tagihan'] - 25569) * 86400);
         $row['tgl_adj'] = gmdate("Y-m-d", ($row['tanggal_adjustment'] - 25569) * 86400);
         $row['nodin_ba'] = $row['nomor_nodinba'];
         unset($row['bulan_tagihan']);unset($row['nomor_nodinba']);unset($row['tanggal_adjustment']);unset($row['']);
         $rowx[] = $row;
       }
     }
      FormAdjustment::insert($rowx);
      $importer->status = "Finish";
      $importer->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FormAdjustment  $formAdjustment
     * @return \Illuminate\Http\Response
     */
    public function show(FormAdjustment $formAdjustment)
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
