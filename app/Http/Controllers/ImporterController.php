<?php

namespace App\Http\Controllers;

use App\Importer;
use Illuminate\Http\Request;

class ImporterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $x= Importer::where('tipe', $request->get('tipe'))
        ->whereDate('created_at', $request->get('start'));
        return datatables()->of($x)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Importer  $importer
     * @return \Illuminate\Http\Response
     */
    public function show(Importer $importer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Importer  $importer
     * @return \Illuminate\Http\Response
     */
    public function edit(Importer $importer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Importer  $importer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Importer $importer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Importer  $importer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Importer $importer)
    {
        //
    }
}
