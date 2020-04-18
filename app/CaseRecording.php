<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseRecording extends Model
{
    use SoftDeletes;

    protected $table = 'recording_tbllaporan';
    protected $primaryKey = 'id';
    protected $fillable = [
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
        'last_response_date',
        'last_response_user',
        'last_response_userid',
        'priority',
        'pic'
    ];
}