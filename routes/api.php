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
		Route::get('permissions', 'BaseController@getPermissions');
		Route::get('users/{user}/', 'UserController@show');
		Route::get('user/trips', 'UserController@listTrips');

		Route::group(['prefix' => 'trips/{trip}/relationships/members'], function() {
			Route::post('/', 'MemberController@addMembers');
			Route::delete('/', 'MemberController@removeMembers');
			Route::post('/accept', 'MemberController@acceptInvite');
			Route::post('/decline', 'MemberController@declineInvite');
		});

		// POIs and users are completely independent of trips whatsoever
		Route::apiResource('pois', 'POIController')->only(['index', 'show']);
		Route::apiResource('users', 'UserController')->only(['show', 'update', 'destroy']);

		// Trip-related resources
		Route::apiResources([
			'trips'                  => 'TripController',
			'trips.reports'          => 'ReportController',
			'trips.reports.sections' => 'SectionController',
			'trips.locations'        => 'LocationController',
			// 'trips.plans'
			// 'trips.payments'
			// 'trips.reports.sections.payments'
		], [ // For automatic scoping
			"parameters" => [
				'reports'   => 'report:id',
				'sections'  => 'section:id',
				'locations' => 'location:id',
			],
		]);
	});
});

Route::any('{all?}', 'BaseController@pageNotFound')
		->where('all', '.*')
		->fallback();
