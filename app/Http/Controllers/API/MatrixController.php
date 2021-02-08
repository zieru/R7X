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
        $alternatif = Alternatif::all()->toArray();
        $no = 1;
        $nilaix = [];
        $nilaid = [];
        $bobotx = [];
        $nilais = Matrix::get();
        $bobots = Kriteria::get();
        foreach ($nilais as $a){
            $nilaix[$a['id_kriteria']][] = $a['nilai'];
            $nilaid[$a['id_kriteria']][] = $a['nilai']* $a['nilai'];
        }
        foreach ($bobots as $a){
            $bobotx[$a['id_kriteria']] = $a['bobot'];
        }
        foreach ($alternatif as $alt){

            $nilais = Matrix::where('id_alternatif',$alt['id_alternatif'])->orderby('id_kriteria')->get();
            //dd($nilais->toArray());
            $data[$no]['no'] = $no;
            $data[$no]['nama'] = $alt['nm_alternatif'];
            $no_kriteria = 1;
            foreach ($nilais as $nilai){
                $kriteria = Kriteria::where('id_kriteria', $nilai->id_kriteria)->firstOrFail()->toArray();
                if($id == 'normalisasi' OR $id == 'bobotnormalisasi') {
                    //$data[$no]['C' . $no_kriteria] = (0) + ($nilai->nilai * $nilai->nilai);
                    $data[$no]['C' . $no_kriteria] = round(($nilai->nilai / sqrt(array_sum($nilaid[$nilai->id_kriteria]))), 3);
                    if ($id == 'bobotnormalisasi'){
                        $data[$no]['C' . $no_kriteria] = $data[$no]['C' . $no_kriteria] * ($kriteria['bobot'] / array_sum($bobotx));
                    }
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
