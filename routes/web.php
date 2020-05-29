<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Front\HomeController@index')->name('front.home');
Route::post('import', 'BillingCollectionController@create')->name('front.BillingImport');
Route::resource('bilco','BillingCollectionController');
Route::prefix('oauth')->group(function(){
    Route::get('/','Front\\HomeController@oauthlogin')->name('front.oauthlogin');
    Route::get('/provider/google','UsersController@redirectToProvider')->name('front.oauthlogin.provider.google');
});

/*Route::get('/', function () {
    return redirect('login');
});*/
Route::get('files/{id}/preview','Front\\FileController@filePreview')->name('front.file.preview');
Route::get('files/{id}/download','Front\\FileController@fileDownload')->name('front.file.download');

Auth::routes();

// NOTE:
// remove the demo middleware before you start on a project, this middleware if only
// for demo purpose to prevent viewers to modify data on a live demo site

// admin
Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function()
{
    // single page
    Route::get('/', 'SinglePageController@displaySPA')->name('admin.spa');

    // resource routes
    Route::resource('users','UserController');
    Route::resource('groups','GroupController');
    Route::resource('permissions','PermissionController');
    Route::resource('files','FileController');
    /*Route::resource('caserecording','FileController');*/
    Route::resource('file-groups','FileGroupController');
});

/*Route::post('redirector', action('UsersController@redirector'));*/
