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

Route::get('/', function() {
    return response()->json(["message" => "API => connected"]);
});

Route::get('/ping', 'SolariumController@ping');
Route::get('/search/{stringSearch}', 'SolariumController@search');
Route::post('/addDocument', 'SolariumController@addDocument');
Route::put('/updateDocument', 'SolariumController@updateDocument');
//Route::get('/search/{stringSearch}', 'SolariumController@search');
