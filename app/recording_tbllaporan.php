<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class recording_tbllaporan extends Model
{
    //
    use SoftDeletes;

    protected $table = 'recording_tbllaporan';
    protected $primaryKey = 'id_laporan';
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
    public function User(){
        return $this->belongsTo('App\User','id_agent');
    }
    public function RecordingLogLaporans(){
        return $this->hasMany('App\recording_tbllaporan','id_laporan');
    }
}
