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

Route::get('/', 'WelcomeController@index');
Route::get('/addressinfo', 'WelcomeController@addressinfo');
Route::get('/review', 'WelcomeController@review');
Route::post('/review', 'WelcomeController@review');
Route::get('/submit/{ordernumer}', 'WelcomeController@submit');
Route::get('/result/{ordernumer}', 'WelcomeController@result');
Route::get('/paypalReturn/{result}/{ordernumber}', 'WelcomeController@paypalReturn');
Route::get('/kaatview', 'WelcomeController@overview');
Route::get('/kaatview/{ordernumber}', 'WelcomeController@acknowledgeSend');
Route::get('/kaatview/delete/{ordernumber}', 'WelcomeController@delete');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
