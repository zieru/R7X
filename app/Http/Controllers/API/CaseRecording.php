<?php

namespace App\Http\Controllers\API;

use App\CaseRecording as CaseRecordingModel;
use App\Http\Controllers\Controller;
use App\Model\CaseRecording\RecordingLogLaporan;
use App\recording_loglaporan;
use App\recording_tbllaporan;
use Illuminate\Http\Request;

class CaseRecording extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $column = array(
            'id_laporan',
            'judul',
            'tipe_layanan',
            'msisdn_menghubungi',
            'msisdn_bermasalah',
            'ket',
            'tgl_kejadian',
            'tgl_kejadian_end',
            'id_agent',
            'waktu',
            'id_co',
            'lastresponse_date',
            'lastresponse_user',
            'lastresponse_userid',
            'priority',
            'pic',
            'updated_at');

        if(request()->has('status')){
            $x= datatables()->of(recording_tbllaporan::with('User')->where('ket', '=',$request->get('status'))) ;
        }else{
            $x= datatables()->of(recording_tbllaporan::with('User')) ;
        }

        //$model = CaseRecordingModel::all();
        //$x = datatables()->eloquent(CaseRecordingModel::query());

        return $x->toJson();
    }
    public function show($id)
    {
        $x= datatables()->of(recording_tbllaporan::with('User')->where('id_laporan',$id));


        return $x->toJson();
    }
    public function logx($id)
    {
        $x= datatables()->of(\App\Model\CaseRecording\RecordingLogLaporan::with('CaseRecording')->where('id_laporan',$id));


        return $x->toJson();
    }
    public function log($id)
    {
        $x = datatables()->of(recording_loglaporan::with('RecordingTblLaporan','User')->where('id_laporan',$id));
        //$x= datatables()->of(\App\Model\CaseRecording\CaseRecording::with('RecordingLogLaporan')->where('id_laporan',1));


        return $x->toJson();
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
        $validate = validator($request->all(),[
            'subject' => 'required|string',
            'remark' => 'required|string',
            'msisdncaller' => 'required|digits_between:6,16',
            'msisdncaller' => 'digits_between:6,16',
        ]);

        if($validate->fails())
        {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        /*$x = $this->InsertNewCase($request->all());*/
        $val = $request->all();

        $CaseRecording = recording_tbllaporan::create([
            'judul' => $val['subject'],
            'tipe_layanan' => $val['calldirection'],
            'msisdn_menghubungi' => $val['msisdncaller'],
            'msisdn_bermasalah' => $val['msisdncaller'],
            'ket' => 'NEW',
            'isi_laporan' => $val['remark'],
            'id_agent'=> auth()->user()->id,
            'priority' => (int)$val['priority'],
            'tgl_kejadian' => $val['dates'][0],
            'tgl_kejadian_end' => $val['dates'][1] ?? null,
            'waktu' => date('Y-m-d H:i:s')

        ]);

        try{
            $CaseRecording->save();
        }
        catch(\Exception $e){
            // do task when error
            return $this->sendResponseBadRequest("Failed to create.");
        }

        //$file = $this->fileGroupRepository->create($request->all());

        //if(!$file)  return $this->sendResponseBadRequest("Failed to create.");

        return $this->sendResponseCreated(null);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

    }
}
