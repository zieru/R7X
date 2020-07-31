<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormRefund extends Model
{
    //
    public $timestamp = true;
  protected $guarded = [];
    public function getmsisdnAttribute($value)
    {
        $ret = $value;
        if(strlen($value) > 8){
            switch(substr($value, 0, 2)){
                case '08' :
                    $ret = '+628'. $value;
                break;
                case 62:
                    $ret = '+'.$value;
                    break;
                default:
                    $ret = '+62'. $value;
            }
        }

        return $ret;
    }
}
