<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Files extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = \App\Files::all();

        $arr = array();
        foreach ($files as $flight) {
          $arr[] =
              array(
                'document_judul' => $flight->name,
                'file_name' => $flight->name,
                'fileext' => $flight->extension,
                'mime' => $flight->file_type,
                'file_size' => $flight->size ,
                'filename' => $flight->name,
                'full_name'=> $flight->name,
                'path' => $flight->token,
                'id' => $flight->id,
                'image_judul'=> $flight->name
                );
            }

        $r = array('data' => $arr);
        //
        return  response()
            ->json($r);
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
