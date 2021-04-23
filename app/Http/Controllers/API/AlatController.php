<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Alat;
use App\Models\AlatMatrix;
use App\Models\Alternatif;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return datatables()->of(Alat::all())->toJson();
    }

    public function alatMatrix(Request $request){
        echo 'x';
        $alat = array();
        AlatMatrix::where('id_alternatif', $request->post('alternatif'))->delete();
        foreach ($request->post('alat') as $alat => $v){
            if(isset ($v[0])){
                $x = AlatMatrix::firstOrCreate(array('id_alternatif' => $request->post('alternatif'),'id_alat' => $v[0]));
            }
        }
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
        $return = [];
        $kriteria = new Alat;
        $kriteria->nm_alternatif = $request->name;
        $kriteria->save();
        $return['data']['value'] =$kriteria->save();
        $return['data']['message'] =($kriteria->save()) ? 'Sukses':'Gagal';
        return $return;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alternatif  $alternatif
     * @return \Illuminate\Http\Response
     */
    public function show(Alat $alat)
    {
        //
        return array('data' => $alat);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alternatif  $alternatif
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alat $alat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alternatif  $alternatif
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alternatif $alternatif)
    {
        //
    }
}
