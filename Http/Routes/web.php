<?php

/*
 * To register a route that needs to be authentication, wrap it in a
 * Route::group() with the auth middleware
 */
 Route::group(['middleware' => 'auth'], function() {
     Route::get('/', 'IndexController@create')->name('create');
     Route::post('/', 'IndexController@store')->name('store');
 });
