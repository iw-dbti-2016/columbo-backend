<?php

Route::group(['prefix' => 'v1'], function() {
	Route::group(['prefix' => 'auth'], function() {
		Route::post('/register', 'Auth\RegisterController@register')->name('api.auth.register');
		Route::post('/login', 'Auth\LoginController@login')->name('api.auth.login');
		Route::patch('/refresh', 'Auth\TokenController@refresh')->name('api.auth.refresh');
		Route::delete('/logout', 'Auth\LogoutController@logout')->name('api.auth.logout');

		Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('api.auth.forgot-password');
		Route::post('/email/resend', 'Auth\VerificationController@resend')->name('api.auth.resend');
	});

	// You can always retrieve your own data
	Route::get('/user', 'BaseController@showUserData')->middleware('auth:sanctum');

	// Other data requires email verification
	Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
		Route::get('users/{user}/', 'UserController@show');
		Route::get('sections/', 'sectionController@list');
		Route::get('sections/{section}', 'sectionController@get');
		Route::get('locations/', 'locationController@list');
		Route::get('locations/{location}', 'locationController@get');
		Route::get('pois/', 'poiController@list');
		Route::get('pois/{poi}', 'poiController@get');
		Route::get('user/trips', 'UserController@listTrips');

		Route::group(['prefix' => 'trips/{trip}'], function() {
			Route::post('/relationships/members', 'MemberController@addMembers');
			Route::delete('/relationships/members', 'MemberController@removeMembers');
			Route::post('/relationships/members/accept', 'MemberController@acceptInvite');
			Route::post('/relationships/members/decline', 'MemberController@declineInvite');
		});

		Route::apiResource('trips', 'TripController');
		Route::apiResource('trips.reports', 'ReportController')
				->parameters(['reports' => 'report:id']);

		Route::group(['prefix' => 'sections'], function() {
			Route::post('/', 'sectionController@store');
			Route::patch('/{section}', 'sectionController@update');
			Route::delete('/{section}', 'sectionController@destroy');
		});

		Route::group(['prefix' => 'locations'], function() {
			Route::post('/', 'locationController@store');
			Route::patch('/{location}', 'locationController@update');
			Route::delete('/{location}', 'locationController@destroy');
		});

		Route::group(['prefix' => 'pois'], function() {
			//
		});
	});
});

Route::any('{all?}', 'BaseController@pageNotFound')
		->where('all', '.*')
		->fallback();
