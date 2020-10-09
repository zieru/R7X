<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BilcoDataSerah extends Model
{
    //
    public $timestamps = false;
    protected $guarded = [];
    //protected $appends = ['tahap'];


    public function getTahapAttribute()
    {
        $tahap = null;
        if(array_key_exists('periode',$this->attributes)){
            $date = $this->attributes['periode'];
            $bilcoenddate = Carbon::createFromFormat('Y-m-d',$date)->endOfMonth();
            $bilcodate = Carbon::createFromFormat('Y-m-d', $date);
            switch ($bilcodate->format('d')){
                case $bilcoenddate->format('d'):
                    $tahap = 1;
                    break;
                default:
                    $tahap = 2;
            }
        }

        return $tahap;
    }
    public function getCekCpAttribute($value)
    {
        switch ($value){
            case 1:
                $value = 'CP';
                break;
            default :
                $value = 'NON CP';
        }
        return $value;
    }
    public function getCekHaloAttribute($value)
    {
        switch ($value){
            case 1:
                $value = 'HALO';
                break;
            default :
                $value = '';
        }
        return $value;
    }
}
