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
}
