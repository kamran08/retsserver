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
Route::post('/directoryCheck', 'DataController@directoryCheck');
Route::post('/uploadThumb1', 'DataController@uploadThumb');
Route::post('/storeToBucket', 'DataController@storeToBucket');
Route::get('resizeImage', 'DataController@resizeImage');
// Route::post('/resizeImagePost', 'DataController@resizeImagePost');
Route::post('/getData1', 'DataController@getData1');
Route::get('/getData', 'DataController@getData');
Route::post('/test', 'DataController@test');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
