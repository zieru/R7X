<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Kriteria;
use App\Models\Alternatif;
use Illuminate\Http\Request;
use App\Models\Matrix;
class MatrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return datatables()->of(Matrix::all())->toJson();
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
        foreach ($request->kriteria as $kriteria => $nilai){
            $matrix = Matrix::firstOrNew(['id_alternatif' => $request->alternatif,'id_kriteria' => $kriteria]);
            $matrix->nilai = $nilai;
            $matrix->save();
        }
        return $return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $data= [];
        $kriteria = Kriteria::all()->toArray();
        $alternatif = Alternatif::all()->toArray();
        $no = 1;
        foreach ($alternatif as $alt){

            $nilais = Matrix::where('id_alternatif',$alt['id_alternatif'])->orderby('id_kriteria')->get();
            //dd($nilais->toArray());
            $data[$no]['no'] = $no;
            $data[$no]['nama'] = $alt['nm_alternatif'];
            $no_kriteria = 1;
            foreach ($nilais as $nilai){
                if($id == 'normalisasi') {
                    $data[$no]['C' . $no_kriteria] = (0) + ($nilai->nilai * $nilai->nilai);
                    $data[$no]['C' . $no_kriteria] = round(($nilai->nilai / sqrt($data[$no]['C' . $no_kriteria])), 3);
                }elseif($id == 'bobotnormalisasi'){
                    $data[$no]['C' . $no_kriteria] = (0) + ($nilai->nilai * $nilai->nilai);
                    $data[$no]['C' . $no_kriteria] = round(($nilai->nilai / sqrt($data[$no]['C' . $no_kriteria])), 3);
                }else{
                    $data[$no]['C'. $no_kriteria]  = $nilai->nilai;
                }

                $no_kriteria++;
            }
            $no++;
        }
        return datatables()->of($data)->toJson();
        //dd($alternatif);
        //dd($matrix);
        //echo $id . $request->type;

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
