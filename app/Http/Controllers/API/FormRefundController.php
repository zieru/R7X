<?php

namespace App\Http\Controllers\API;
use App\FormAdjustment;
use Carbon\Carbon;
use App\Imports\FormAdjustmentImport;
use App\Importer;

use App\FormRefund;

use Auth;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $period = DateTime::createFromFormat('Y-m', $request->get('period'));
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
            ->selectRaw("$tbl_grup,count(msisdn) as msisdn,format(new_balance - balance,0) as nominal")
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
         if($row['shop'] != null) {
             $row['import_batch'] = $importer->id;
             $row['author'] = Auth::user()->id;
             $row['tanggal_permintaan'] = gmdate("Y-m-d", ($row['tgl_permintaan'] - 25569) * 86400);
             $row['tanggal_eksekusi'] = gmdate("Y-m-d", ($row['tgl_eksekusi'] - 25569) * 86400);

             unset($row['tgl_permintaan']);
             unset($row['tgl_eksekusi']);
             $rowx = $row;
         }
     }
      FormRefund::insert($rowx);
      $importer->status = "Finish";
      $importer->save();
      echo 'x';
        //
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
