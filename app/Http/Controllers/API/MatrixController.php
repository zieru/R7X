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
        $countbobot = [];
        foreach ($alternatif as $alt){

            $nilais = Matrix::where('id_alternatif',$alt['id_alternatif'])->orderby('id_kriteria')->get();
            //dd($nilais->toArray());
            $data[$no]['no'] = $no;
            $data[$no]['nama'] = $alt['nm_alternatif'];
            $no_kriteria = 1;
            $datatemp = [];
            $normalisasi = [];
            foreach ($nilais as $nilai){
                $kriteria = Kriteria::where('id_kriteria', $nilai->id_kriteria)->firstOrFail()->toArray();
                $data[$no]['C' . $no_kriteria] = round(($nilai->nilai / sqrt(array_sum($nilaid[$nilai->id_kriteria]))), 3);
                $countbobot[$no][] = $countbobotx =  $data[$no]['C' . $no_kriteria] * ($kriteria['bobot'] / array_sum($bobotx));
                $dataori[$no]['C' . $no_kriteria] = $nilai->nilai;
                if($id == 'normalisasi' OR $id == 'bobotnormalisasi' OR $id == 'nilaiideal'  OR $id == 'jarakideal' OR $id=='preferensi' oR $id=='ranking') {
                    $data[$no]['C' . $no_kriteria] = (0) + ($nilai->nilai * $nilai->nilai);
                }else{
                    $data[$no]['C' . $no_kriteria] = $nilai->nilai;
                }
                $no_kriteria++;
            }
            $no++;
        }
        if($id == 'jarakideal'){
            foreach ($data as $val){

//                dd($countbobot);
            }
        }
        if($id === 'normalisasi'){
            $data = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
        }elseif($id == 'bobotnormalisasi'){
            $x = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
            $data = $this->normalisasibobot($dataori,$x,$bobots->toArray(),$nilais,$alternatif);
        }
        elseif($id == 'nilaiideal'){
            $x = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
            $data = $this->normalisasibobot($dataori,$x,$bobots->toArray(),$nilais,$alternatif);
            $xs = $this->nilaiideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif);
            $data = [];
            $data['data'] = $xs;
            //dd($data);
        }
        elseif($id == 'jarakideal'){
            $x = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
            $normalisasibobot = $data = $this->normalisasibobot($dataori,$x,$bobots->toArray(),$nilais,$alternatif);
            $data = $this->nilaiideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif);
            $xs = $this->jarakideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $data = [];
            $data['data'] = $xs;
//            dd($data);
        }
        elseif($id == 'preferensi'){
            $x = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
            $normalisasibobot = $data = $this->normalisasibobot($dataori,$x,$bobots->toArray(),$nilais,$alternatif);
            $data = $this->nilaiideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif);
            $data = $this->jarakideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $xs = $this->preferensi($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $data = [];
            $data['data'] = $xs;
        }
        elseif($id == 'ranking'){
            $x = $this->normalisasi($dataori,$data,$kriteria,$nilais,$alternatif);
            $normalisasibobot = $data = $this->normalisasibobot($dataori,$x,$bobots->toArray(),$nilais,$alternatif);
            $data = $this->nilaiideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif);
            $data = $this->jarakideal($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $data = $this->preferensi($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $xs = $this->ranking($dataori,$data,$bobots->toArray(),$nilais,$alternatif,$normalisasibobot);
            $data = [];
            $data = $xs;
        }
        return datatables()->of($data)->toJson();
        //dd($alternatif);
        //dd($matrix);
        //echo $id . $request->type;

    }
    private function ranking($dataori,$preferensi,$bobot,$nilais,$alternatif,$normalisasibobot){
        usort($preferensi, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });
        $ret = [];
        $r = 1;
        foreach ($preferensi as $x){
            $ret[] = array('nama' => $x['nama'], 'nilai'=> $x['nilai'], 'rank'=> $r);
            $r++;
        }
        return $ret;
    }
    private function preferensi($dataori,$jarakideal,$bobot,$nilais,$alternatif,$normalisasibobot){
        $ret = [];
        foreach($jarakideal as $x => $v){
            $nilai = (float)$v[0]/((float)$v[0] + (float)$v[1]);
            $ret[] = array('nama' => $x, 'nilai' => (float) $nilai);
        }
        return $ret;
    }
    private function jarakideal($dataori,$nilaiideal,$bobot,$nilais,$alternatif,$normalisasibobot){
        $posx = $negx = $nilaiidealx = $pos = $neg = $normalisasibobotx = [];
        foreach ($normalisasibobot as $x){
            $normalisasibobotx[] = array_filter($x, function($key) {
                return strpos($key, 'C') === 0;
            }, ARRAY_FILTER_USE_KEY);
        }
        $carray_count = array_filter($normalisasibobot[1], function($key) {
            return strpos($key, 'C') === 0;
        }, ARRAY_FILTER_USE_KEY);
        foreach ($nilaiideal as $x){
            $nilaiidealx[] = $x;
        }
        $y = 0;
        //dd($nilaiidealx);
        //dd($nilaiidealx);
        //dd($nilaiidealx);
        foreach ($normalisasibobotx as $x){
            for ($i = 0; $i < sizeof($bobot); $i++) {
                //echo $i .'. ';
                $pos[$y][] = (float) number_format(pow((float) $x['C'.($i+1)] - (float) $nilaiidealx[$i][1],2),4) ;
                $neg[$y][] = (float)  number_format(pow((float) $x['C'.($i+1)] - (float) $nilaiidealx[$i][0],2),4) ;
                //echo sprintf('%s - %s <br>',(float) $x['C'.($i+1)] , (float) $nilaiidealx[$i][0]);
            }
            $y++;
        }
        $y = 0;

        $ret = [];
        foreach ($normalisasibobot as $x) {
                $posx = number_format(sqrt(array_sum($pos[$y])),4);
                $negx=  number_format(sqrt(array_sum($neg[$y])),4);
                $ret[$x['nama']] = [$negx,$posx];
            $y++;
        }

        return $ret;
    }

    private function nilaiideal($dataori,$data,$bobot,$nilais,$alternatif){
        $carray_count = array_filter($data[1], function($key) {
            return strpos($key, 'C') === 0;
        }, ARRAY_FILTER_USE_KEY);
        $bobotall = [];
        for ($i = 1; $i <= sizeof($data); $i++) {
            for ($y = 1; $y <= sizeof($carray_count); $y++) {
                $bobotall[$y][] = $data[$i]['C'.$y];
            }
        }

        $datatemp = [];
        for ($i = 1; $i <= sizeof($bobot); $i++) {
                $datatemp[$bobot[$i-1]['nama_kriteria']][] = number_format(min($bobotall[$i]),4);
                $datatemp[$bobot[$i-1]['nama_kriteria']][] = number_format(max($bobotall[$i]),4);
        }
        return $datatemp;
    }

    private function normalisasibobot($dataori,$data,$bobot,$nilais,$alternatif){
        $bobotall = [];
        $bobotpros = [];
        foreach ($bobot as $bbt){
            $bobotall [] = $bbt['bobot'];
        }
        foreach ($bobot as $bbt){
            $bobotpros [] = $bbt['bobot'] / array_sum($bobotall);
        }
        $carray_count = array_filter($data[1], function($key) {
            return strpos($key, 'C') === 0;
        }, ARRAY_FILTER_USE_KEY);

        for ($i = 1; $i <= sizeof($dataori); $i++) {
            for ($y = 1; $y <= sizeof($carray_count); $y++) {
                $dataori[$i]['C'.$y] = number_format($data[$i]['C'.$y] * $bobotpros[$y-1],4);
            }
            $dataori[$i]['no'] = $data[$i]['no'];
            $dataori[$i]['nama'] = $data[$i]['nama'];
        }
        return $dataori;
    }

    private function normalisasi($dataori,$data,$kriteria,$nilais,$alternatif){
            $oridata = $data;
            $carray_count = array_filter($data[1], function($key) {
                return strpos($key, 'C') === 0;
            }, ARRAY_FILTER_USE_KEY);
            $cur = 1;
            $newdat = [];
            while(sizeof ($data) >= $cur ){
                $c = 0;
                for ($i = 1; $i <= sizeof($carray_count); $i++) {
                    if(isset($newdat[$i])){
                        $newdat[$i] = $newdat[$i] + $data[$cur]['C'.$i];
                    }else{
                        $newdat[$i] = $data[$cur]['C'.$i];
                    }
                }
                $cur ++;
            }
            //sqrt
            for ($i = 1; $i <= sizeof($carray_count); $i++) {
                $newdat[$i] = sqrt ($newdat[$i]);
            }
            $cur = 1;
            for ($i = 1; $i <= sizeof($dataori); $i++) {
                for ($y = 1; $y <= sizeof($carray_count); $y++) {
                    $dataori[$i]['C'.$y]  = number_format($dataori[$i]['C'.$y] / $newdat[$i],4);
                }
                $dataori[$i]['no'] = $data[$i]['no'];
                $dataori[$i]['nama'] = $data[$i]['nama'];
            }
            return $dataori;
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
