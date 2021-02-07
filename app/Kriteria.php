<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    //
    public $timestamps = false;
    public $table = 'kriteria';
    protected $guarded = [];
    public function kriteriaMatrix()
    {
        return $this->belongsToMany('App\Models\Matrix', 'id_kriteria', 'id_kriteria');
    }
    public function alternatif()
    {
        return $this->belongsToMany('App\Models\Alternatif','nilai_matrik','id_kriteria','id_kriteria');
    }
    /*public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Jakarta')
            ->toDateTimeString()
            ;
    }
    public function getUpdatedAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Jakarta')
            ->toDateTimeString()
            ;
    }*/

}
