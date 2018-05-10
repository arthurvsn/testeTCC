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

Route::get('/search/{stringSearch}', 'SolariumController@search');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
