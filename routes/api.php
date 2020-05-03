<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
/*Route::post('/caserecording/createnew', 'CaseRecording@createnew');
Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function() {
    Route::post('/caserecording/createnew', 'CaseRecording@createnew');
    Route::get('/caserecording/createnew', 'CaseRecording@createnew');
});*/
Route::prefix('admin')->namespace('API')->middleware(['auth'])->group(function() {
    Route::post('/caserecording/createnew', 'CaseRecording@Store');
    Route::get('/caserecording', 'CaseRecording@index');
    Route::get('/caserecording/{caserecording}/log', 'CaseRecording@log');
    Route::get('/caserecording/{caserecording}/show', 'CaseRecording@show');

    route::apiResource('filestorage','Files');
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'UsersController@login');
    Route::post('register', 'UsersController@register');
    Route::post('refreshtoken', 'UsersController@refreshToken');

    //Route::get('logout', 'UsersController@logout')->middleware('auth:api');

    Route::get('/unauthorized', 'UsersController@unauthorized');
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::post('logout', 'UsersController@logout');
        Route::post('details', 'UsersController@details');
    });
});
