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


Route::get('/getData', 'DataController@getData');
Route::get('/getLocation', 'RetsController@getLocation');
Route::get('/storeImages', 'RetsController@storeImages');
Route::get('/getDatadirectoryCheck', 'DataController@directoryCheck');
Route::get('/resorce', 'DataController@resorce');
// Route::get('/getData', 'DataController@getData');
Route::get('/', function () {
    return view('welcome');
});

// source normal
// svg normal