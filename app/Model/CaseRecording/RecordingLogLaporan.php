<?php

namespace App\Model\CaseRecording;

use Illuminate\Database\Eloquent\Model;

class RecordingLogLaporan extends Model
{
    protected $table = 'recording_loglaporan';
    protected $primaryKey = 'id_log_laporan';
    protected $fillable = [
        'id_agent',
        'ket',
        'id_laporan',
        'rec_url',
        'isi'
    ];

    public function RecordingLog(){
        return $this->hasMany('App\User','id_agent');
    }



    public function CaseRecording(){
        return $this->belongsTo(\App\Model\CaseRecording\CaseRecording::class,'id_laporan');
    }
}
