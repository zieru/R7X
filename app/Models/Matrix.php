<?php

namespace App\Models;

use App\Kriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matrix extends Model
{
    use HasFactory;
    public $table = 'nilai_matrik';

    protected $primaryKey = "id_matrik";
    protected $guarded = [];
    public $timestamps = false;
    public function alternatif()
    {
        return $this->belongsToMany(Alternatif::class);
    }
    public function kriteria()
    {
        return $this->belongsToMany(Kriteria::class);
    }
}
