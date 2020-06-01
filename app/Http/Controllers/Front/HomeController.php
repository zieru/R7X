<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/8/2017
 * Time: 5:11 PM
 */

namespace App\Http\Controllers\Front;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Session;
use Illuminate\Routing\Redirector;

class HomeController extends FrontController
{
    public function index()
    {
        return view('layouts.front');
    }

    public function oauthlogin(Request $request){

         //$request->session()->put('redirect',$request->get('redirect'));
        $request->session()->put('redirect',$request->fullUrl());
        Cookie::queue('redirect',$request->get('redirect'));
        if(Session::has('token_data')){
            $tokendata = \Session::get('token_data');
            var_dump($tokendata);
            if(request()->has('redirect')){
                $param = '?access_token='.$tokendata['access_token'].'&refresh_token='.$tokendata['refresh_token'];
                return redirect(request()->get('redirect').$param);
            }
        }

        return view('layouts.front.view.oauthlogin');
    }
}
