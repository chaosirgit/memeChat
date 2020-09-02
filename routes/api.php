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

//用户端
Route::prefix('v1')->group(function (){
    //无需授权的api
    Route::post('/login','Api\UserController@login'); //验证是否绑定

    //需要授权的api
    Route::middleware('auth:api')->group(function (){
        Route::get('/user','Api\UserController@user'); //获取个人信息
        Route::get('/index','Api\IndexController@isConnect'); //是否链接socket
        Route::post('/upload','Api\IndexController@upload'); //上传图片base64
        Route::post('/user','Api\UserController@bindUserInfo'); //绑定个人信息
        Route::post('/user/bindPhone','Api\UserController@bindUserPhone'); //绑定手机号
    });

});
