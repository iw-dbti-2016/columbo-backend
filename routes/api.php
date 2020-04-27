<?php

use Illuminate\Support\Facades\Route;

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

		// POI is completely independent of trip whatsoever
		Route::apiResource('pois', 'POIController')->only(['index', 'show']);

		Route::get('users/{user}/', 'UserController@show');
		Route::get('locations/', 'LocationController@list');
		Route::get('locations/{location}', 'LocationController@get');
		Route::get('user/trips', 'UserController@listTrips');

		Route::group(['prefix' => 'trips/{trip}'], function() {
			Route::post('/relationships/members', 'MemberController@addMembers');
			Route::delete('/relationships/members', 'MemberController@removeMembers');
			Route::post('/relationships/members/accept', 'MemberController@acceptInvite');
			Route::post('/relationships/members/decline', 'MemberController@declineInvite');
		});

		Route::apiResource('users', 'UserController')->only(['show', 'update', 'destroy']);

		Route::apiResources([
			'trips'                  => 'TripController',
			'trips.reports'          => 'ReportController',
			'trips.reports.sections' => 'SectionController',
			// 'trips.plans'
			// 'trips.payments'
			// 'trips.reports.sections.payments'
		], [ // For automatic scoping
			"parameters" => [
				'reports'  => 'report:id',
				'sections' => 'section:id',
			],
		]);

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
