<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kriteria;
class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return datatables()->of(Kriteria::all())->toJson();
        //
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
        $kriteria = new Kriteria;
        $kriteria->nama_kriteria = $request->namakriteria;
        $kriteria->bobot = $request->bobot;
        $kriteria->poin1 = $request->poin1;
        $kriteria->poin2 = $request->poin2;
        $kriteria->poin3 = $request->poin3;
        $kriteria->poin4 = $request->poin4;
        $kriteria->poin5 = $request->poin5;
        $kriteria->sifat = $request->sifat;
        $kriteria->save();
            $return['data']['value'] =$kriteria->save();
            $return['data']['message'] =($kriteria->save()) ? 'Sukses':'Gagal';
        return $return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
