<?php

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

Route::group(['prefix' => 'auth'], function() {
	Auth::routes(['verify' => true]);
});

Route::get('/', function() {
	return view('home');
})->middleware(['withoutTokenCookies']);

Route::get('/app/{all?}', function(Request $request) {
	return response()->view('app');
})->where('all', '.*')
	->middleware(['auth:api', 'verified'])
	->name('app');

Route::any('{all?}', function() {
	abort(404);
})->where('all', '.*')->fallback();
