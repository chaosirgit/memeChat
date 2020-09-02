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

Route::prefix('v1')->group(function (){
    //无需授权的admin
    Route::post('/login','Admin\IndexController@login'); //验证是否绑定


    //需要授权的admin
    Route::middleware('auth:admin')->group(function (){
        Route::post('/upload','Admin\IndexController@upload'); //图片上传
        Route::get('/setting','Admin\SettingController@getSettings'); //
        Route::post('/setting','Admin\SettingController@postAdd'); //

        Route::get('/info','Admin\IndexController@info'); //获取个人信息
        Route::post('/logout','Admin\IndexController@logout'); //登出


    });

});
