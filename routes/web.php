<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{all}', function() {
	$browserCookie = Cookie::make('browser-validation', Hash::make("true"), 0, $path=null, $domain=null, $secure=false, $httpOnly=true, $raw=false, $sameSite='strict');

	return response()
				->view('welcome')
				->cookie($browserCookie);
})->where('all', '.*');
