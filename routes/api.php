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


Route::prefix('telegram')->group(function(){
    Route::get('dashboardApiCompare', 'BillingCollectionController@dashboardApiCompare');
});
Route::apiResource('notifier', 'NotifierController');
Route::prefix('v1')->middleware(['CheckClientCredentials','auth:api'])->group(function() {
    Route::apiResource('kriteria', 'API\KriteriaController');
    Route::apiResource('alternatif', 'API\AlternatifController');
    Route::apiResource('bilcodataserah', 'API\BilcoDataSerahController');
    Route::apiResource('bilcodataserahcekbayar', 'API\BilcodataserahCekBayarController');
    Route::any('bilcodataserahcekbayars/cekupdate', 'API\BilcodataserahCekBayarController@cekupdate');
    Route::any('bilcodataserahcekbayarmom', 'API\BilcodataserahCekBayarController@mom');
    Route::any('bilcodataserahcekbayars/export', 'API\BilcodataserahCekBayarController@export');
    Route::get('bilcodataserahkpi', 'API\BilcoDataSerahController@getKpi');
    Route::get('bilcodataserahchart', 'API\BilcoDataSerahController@chart');
    Route::get('bilcodataserahexport', 'API\BilcoDataSerahController@export');
    Route::apiResource('importer', 'ImporterController');
    Route::apiResource('config', 'API\ConfigController');
    Route::any('billCo/dashboard/poc', 'BillingCollectionController@dashboardApiPOC')->name('api.v1.bilco.dashboard.poc');
    Route::any('billCo/dashboard', 'BillingCollectionController@dashboardApi')->name('api.v1.bilco.dashboard');
    Route::any('billCo/dashboard/area', 'BillingCollectionController@dashboardApiArea')->name('api.v1.bilco.dashboard.area');
    Route::any('billCo/dashboard/compare', 'BillingCollectionController@dashboardApiCompare')->name('api.v1.bilco.dashboard.compare');
    Route::any('billCo/dashboard/comparedev', 'BillingCollectionController@dashboardApiCompareDev')->name('api.v1.bilco.dashboard.comparedev');
    Route::any('billCo/dashboard/target', 'BillingCollectionController@dashboardApiTarget')->name('api.v1.bilco.dashboard.target');
    Route::get('billCo/dashboard/inputtarget', 'BillingCollectionController@targetDateYear')->name('api.v1.bilco.dashboard.inputtarget');
    Route::post('billCo/dashboard/inputtarget', 'BillingCollectionController@targetDateYearPost')->name('api.v1.bilco.dashboard.inputtargetpost');

    route::apiResource('adjustment','API\FormAdjustmentController');
    Route::get('adjustmentreport', 'API\FormAdjustmentController@report')->name('api.v1.bilco.adjustment.report');
    Route::get('adjustmentreportreason', 'API\FormAdjustmentController@reportreason')->name('api.v1.bilco.adjustment.report.reason');
    route::apiResource('refund','API\FormRefundController');
  Route::group(['prefix' => 'manage'], function () {
    Route::group(['prefix' => 'user'], function () {
      Route::get('list', 'UsersController@userlist')->name('api.v1.manage.user.list');
      Route::post('activateuser', 'UsersController@activateUser')->name('api.v1.manage.user.activateUser');
    });
  });

});
Route::group(['prefix' => 'v1'], function () {


    Route::post('authcheck', 'UsersController@authCheck')->name('api.v1.authCheck');
    Route::post('login', 'UsersController@login')->name('api.v1.login');
    Route::get('login/google', 'UsersController@redirectToProvider')->name('api.v1.login.google');
    Route::get('login/google/callback', 'UsersController@handleProviderCallback')->name('api.v1.login.google.callback');
    Route::post('register', 'UsersController@register');
    Route::post('refreshtoken', 'UsersController@refreshToken');

    //Route::get('logout', 'UsersController@logout')->middleware('auth:api');

    Route::get('/unauthorized', 'UsersController@unauthorized');
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::get('datetime', 'UsersController@datetime')->name('api.v1.manage.user.datetime');
        Route::post('logout', 'UsersController@logout');
        Route::get('details', 'UsersController@details');
    });
});
