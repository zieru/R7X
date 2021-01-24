<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Alternatif;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return datatables()->of(Alternatif::all())->toJson();
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
        $kriteria = new Alternatif;
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
    public function show(Alternatif $alternatif)
    {
        //
        return array('data' => $alternatif);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alternatif  $alternatif
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alternatif $alternatif)
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
