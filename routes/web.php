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
Route::get('/featchRdData', 'RetsController@featchRdData');
Route::get('/getDatadirectoryCheck', 'DataController@directoryCheck');
Route::get('/getOpenHouseData', 'DataController@getOpenHouseData');
Route::get('/resorce', 'DataController@resorce');
Route::get('/resorce2', 'DataController@resorce2');
Route::get('/getOpenHouseData', 'DataController@getOpenHouseData');
// Route::get('/getLocationTest', 'DataController@getLocationTest');
Route::get('/deletedublicateData', 'DataController@deletedublicateData');
// Route::get('/storeImages', 'RetsController@storeImages');//updateRa2Data

Route::get('/testUpdateCheck', 'UpdateController@updateRa2Data');
Route::get('/testUpdateCheck1', 'UpdateController@updateRD_1Data');
Route::get('/removeAllPreviousImages', 'UpdateController@removeAllPreviousImages');
Route::get('/testdeletes', 'UpdateController@testdelete');
Route::get('/rdupdatefrom2021', 'UpdateController@rdupdatefrom2021');
Route::get('/sendAlldata', 'UpdateController@sendAlldata');
Route::get('/checkRestofImages', 'UpdateController@updateDoplicateData');
Route::get('/SendImagesToMainServer', 'UpdateController@SendImagesToMainServer');





// Route::get('/createmissingrequest', 'UpdateController@testMethod');


Route::get('/', function () {
    return view('welcome');
});