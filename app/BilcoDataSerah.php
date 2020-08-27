<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BilcoDataSerah extends Model
{
    //
    public $timestamps = false;
    protected $guarded = [];
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
