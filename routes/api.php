<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/timestamp/in', 'TimestampApiController@in');
Route::post('/timestamp/out', 'TimestampApiController@out');
Route::put('/timestamp/id/{id}', 'TimestampApiController@edit');
Route::delete('/timestamp/id/{id}', 'TimestampApiController@delete');
