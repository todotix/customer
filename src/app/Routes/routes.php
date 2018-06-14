<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix'=>'process'], function(){
	if(!config('customer.custom.register')){
    	Route::post('registro', 'ProcessController@postRegistro');
	}
    Route::get('check-ci/{ci_number}', 'ProcessController@getCheckCi');
});