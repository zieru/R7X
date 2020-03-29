<?php

namespace App\Http\Controllers\Admin;

use App\Components\File\Models\File;
use App\Components\File\Repositories\FileRepository;
use App\Components\File\Services\FileService;
use Illuminate\Http\Request;
use Auth;
use League\Flysystem\FileNotFoundException;

class CaseRecording extends AdminController
{
    public function index(){

    }
}