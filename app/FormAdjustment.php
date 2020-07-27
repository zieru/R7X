<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormAdjustment extends Model
{
    //
    public $timestamp = true;
  protected $guarded = [];
  public function User(){
    return $this->belongsTo('App\User','author');
  }
}

