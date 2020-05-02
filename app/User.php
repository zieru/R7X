<?php

namespace App;

use App\Model\CaseRecording\RecordingLogLaporan;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function recording_loglaporan(){

        return $this->hasMany(recording_loglaporan::class)->withDefault(function () {
            return new recording_loglaporan();
        });
    }

    public function recording_tbllaporan(){
        return $this->hasMany(recording_tbllaporan::class);
    }

}