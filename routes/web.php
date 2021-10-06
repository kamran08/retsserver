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


Route::post('/storeDataFromDataServer', 'DataController@storeDataFromDataServer');
Route::post('/storeImageDataFromDataServer', 'DataController@storeImageDataFromDataServer');
Route::get('/getData', 'DataController@getData');
Route::get('/getLocation', 'RetsController@getLocation');
Route::get('/storeImages', 'RetsController@storeImages');
Route::get('/getDatadirectoryCheck', 'DataController@directoryCheck');
Route::get('/getOpenHouseData', 'DataController@getOpenHouseData');
Route::get('/resorce', 'DataController@resorce');
Route::get('/resorce2', 'DataController@resorce2');
Route::get('/getOpenHouseData', 'DataController@getOpenHouseData');
// Route::get('/getLocationTest', 'DataController@getLocationTest');
Route::get('/deletedublicateData', 'DataController@deletedublicateData');
// Route::get('/storeImages', 'RetsController@storeImages');//updateRa2Data

Route::get('/testUpdateCheck', 'UpdateController@updateRa2Data');
// Route::get('/removeAllPreviousImages', 'UpdateController@storeImages');
Route::get('/testdelete', 'UpdateController@testdelete');

// Route::get('/createmissingrequest', 'UpdateController@testMethod');


Route::get('/', function () {
    return view('welcome');
});