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
	// Only auth things happening in the browser are:
	// Verification of email-address after registration.
	Route::get('/email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
	// Showing password reset form after requesting it via app.
	Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
	// Resetting of password after submitting password reset form.
	Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
});

Route::get('/', function() {
	return view('home');
})->middleware(['withoutTokenCookies'])->name('home');

Route::get('/app/{all?}', function(Request $request) {
	return response()->view('app');
})->where('all', '.*')
	->middleware(['auth:api', 'verified'])
	->name('app');

Route::any('{all?}', function() {
	abort(404);
})->where('all', '.*')->fallback();
