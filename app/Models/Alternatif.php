<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;
    protected $primaryKey = "id_alternatif";
    public $timestamps = false;
    public $table = 'alternatif';

    public function alternatifMatrix()
    {
        return $this->hasMany('App\Models\Matrix', 'id_alternatif', 'id_alternatif');
    }
    public function kriteria()
    {
        return $this->belongsToMany('App\Kriteria','nilai_matrik','id_alternatif','id_alternatif');
    }
}
