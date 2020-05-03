<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Model\CaseRecording\RecordingLogLaporan;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public function recording_loglaporan(){

        return $this->hasMany(recording_loglaporan::class)->withDefault(function () {
            return new recording_loglaporan();
        });
    }

    public function recording_tbllaporan(){
        return $this->hasMany(recording_tbllaporan::class);
    }

}