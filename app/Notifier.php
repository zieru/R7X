<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- This is required

class Notifier extends Model
{
    //
    use SoftDeletes;
    protected $guarded = [];
}
