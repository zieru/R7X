<?php

namespace App\Model\CaseRecording;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseRecording extends Model
{
    use SoftDeletes;

    protected $table = 'recording_tbllaporan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'isi_laporan',
        'judul',
        'tipe_layanan',
        'msisdn_menghubungi',
        'msisdn_bermasalah',
        'ket',
        'tgl_kejadian',
        'tgl_kejadian_end',
        'id_agent',
        'waktu',
        'id_co',
        'lastresponse_date',
        'lastresponse_user',
        'lastresponse_userid',
        'priority',
        'pic',
        'updated_at'
    ];

    public function RecordingLogLaporan(){

        return $this->hasMany(\App\Model\CaseRecording\RecordingLogLaporan::class,'id_laporan');
    }
}