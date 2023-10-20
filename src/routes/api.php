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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('jira/workhours', 'App\Http\Controllers\JiraController@getWorkhours');
Route::post('jira/subtask', 'App\Http\Controllers\JiraController@createSubtask');
Route::get('trujay/entities', 'App\Http\Controllers\TruJayController@getEntities');
Route::get('trujay/{entity}/count', 'App\Http\Controllers\TruJayController@getEntityCount');
Route::get('trujay/{entity}/customFields', 'App\Http\Controllers\TruJayController@getEntityCustomFields');
