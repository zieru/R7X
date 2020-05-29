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
    Route::any('billCo/dashboard/poc', 'BillingCollectionController@dashboardApiPOC')->name('api.v1.bilco.dashboard.poc');
    Route::any('billCo/dashboard', 'BillingCollectionController@dashboardApi')->name('api.v1.bilco.dashboard');
    Route::middleware(['auth'])->group(function() {

    });


    Route::post('login', 'UsersController@login')->name('api.v1.login');
    Route::get('login/google', 'UsersController@redirectToProvider')->name('api.v1.login.google');
    Route::get('login/google/callback', 'UsersController@handleProviderCallback')->name('api.v1.login.google.callback');
    Route::post('register', 'UsersController@register');
    Route::post('refreshtoken', 'UsersController@refreshToken');

    //Route::get('logout', 'UsersController@logout')->middleware('auth:api');

    Route::get('/unauthorized', 'UsersController@unauthorized');
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::post('logout', 'UsersController@logout');
        Route::get('details', 'UsersController@details');
    });
});
