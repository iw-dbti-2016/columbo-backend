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

Route::group(['prefix' => 'v1'], function() {
	Route::middleware('auth:api')->get('/user', function (Request $request) {
	    return $request->user();
	});

	Route::get('/user', function(Request $request) {
		return ["success" => true, "data" => $request->user()];
	})->middleware('auth:api');

	Route::group(['prefix' => 'auth'], function() {
		Route::post('/register', 'Auth\APIAuthController@register')->name('api.auth.register');
		Route::post('/login', 'Auth\APIAuthController@login')->name('api.auth.login');
	});
});

Route::any('{all}', function() {
	return response()->json([
		"success" => false,
		"message" => "Page not Found.",
	], 404);
})->where('all', '.*')->fallback();
