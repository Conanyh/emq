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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('store/queue', 'TestController@store');
Route::get('amqp/send', 'AmqpSendController@sendTask');


Route::get('sending', 'RabbitMQController@sending');
Route::get('receiving', 'RabbitMQController@receiving');


Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');
});


Route::group([
    'prefix' => 'lara'
], function () {
    
});

