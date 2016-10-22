<?php

Route::group(['middleware' => 'web', 'prefix' => 'core', 'namespace' => 'Laracraft\Core\Http\Controllers'], function()
{
    Route::get('/', 'CoreController@index');
});
