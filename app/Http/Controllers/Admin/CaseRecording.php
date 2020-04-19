<?php

namespace App\Http\Controllers\Admin;

use App\CaseRecording as CaseRecordingModel;
use App\Components\File\Models\File;
use App\Components\File\Repositories\FileRepository;
use App\Components\File\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use League\Flysystem\FileNotFoundException;
use Yajra\DataTables\DataTables;

class CaseRecording extends AdminController
{

    public function __construct()
    {

    }

    public function index(Request $request){

        if(request()->has('status')){
            $x= datatables()->of(CaseRecordingModel::where('ket', '=',$request->get('status'))) ;
        }else{
            $x= datatables()->of(CaseRecordingModel::all()) ;
        }

        $model = CaseRecordingModel::all();
//        $x = datatables()->eloquent(CaseRecordingModel::query());

        return $x->toJson();
    }

    public function InsertNewCase($request){

    }


    public function Store(Request $request){

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

        $CaseRecording = CaseRecordingModel::create([
                'judul' => $val['subject'],
                'tipe_layanan' => $val['calldirection'],
                'msisdn_menghubungi' => $val['msisdncaller'],
                'msisdn_bermasalah' => $val['msisdncaller'],
                'ket' => $val['remark'],
                'id_agent'=> auth()->user()->id,
                'priority' => (int)$val['priority'],
                'tgl_kejadian' => $val['dates'][0],
                'tgl_kejadian_end' => $val['dates'][1] ?? null,
                'waktu' => date('Y-m-d H:i:s')

            ]);

        $CaseRecording->save();
        //$file = $this->fileGroupRepository->create($request->all());

        //if(!$file)  return $this->sendResponseBadRequest("Failed to create.");

        return $this->sendResponseCreated(null);
    }
}