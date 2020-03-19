<?php

Route::group(['prefix' => 'auth'], function() {
	// Only auth things happening in the browser are:

	// Verification of email-address after registration.
	Route::get('/email/verify/{id}/{hash}', 'Auth\VerificationController@verify')
			->name('verification.verify');

	// Showing password reset form after requesting it via app.
	Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')
			->name('password.reset');

	// Resetting of password after submitting password reset form.
	Route::post('/password/reset', 'Auth\ResetPasswordController@reset')
			->name('password.update');
});

Route::get('/', 'BaseController@home')
		->name('home');

Route::any('{all?}', 'BaseController@pageNotFound')
		->where('all', '.*')
		->fallback();
