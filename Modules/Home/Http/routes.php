<?php

Route::group(['middleware' => 'api', 'prefix' => 'home', 'namespace' => 'Modules\Home\Http\Controllers'], function()
{
    Route::get('/test', 'TestController@test');
    Route::get('/testRedis', 'TestController@testRedis');
});
