<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class recording_loglaporan extends Model
{
    //
    protected $table = 'recording_loglaporan';
    protected $primaryKey = 'id_log_laporan';
    protected $fillable = [
        'id_agent',
        'ket',
        'id_laporan',
        'rec_url',
        'isi'
    ];

    public function User(){
        return $this->belongsTo('App\User','id_agent');
    }



    public function RecordingTblLaporan(){
        return $this->belongsTo('App\recording_loglaporan','id_laporan');
    }
}
