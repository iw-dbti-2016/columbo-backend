<?php

use Illuminate\Http\Request;
use Columbo\Http\Resources\User;

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
	Route::group(['prefix' => 'auth'], function() {
		Route::post('/register', 'Auth\API\RegisterController@register')->name('api.auth.register');
		Route::post('/login', 'Auth\API\LoginController@login')->name('api.auth.login');
		Route::patch('/refresh', 'Auth\APIAuthController@refresh')->name('api.auth.refresh');
		Route::delete('/logout', 'Auth\API\LoginController@logout')->name('api.auth.logout');

		Route::post('/password/email', 'Auth\API\ForgotPasswordController@sendResetLinkEmail')->name('api.auth.forgot-password');
		Route::post('/email/resend', 'Auth\API\VerificationController@resend')->name('api.auth.resend');
	});

	// Retreiving of entities made public for now (faster appdev)
	Route::get('users/{user}/', 'UserController@show');
	Route::get('trips', 'tripController@list');
	Route::get('trips/{trip}', 'tripController@get');
	Route::get('reports/', 'reportController@list');
	Route::get('reports/{report}', 'reportController@get');
	Route::get('sections/', 'sectionController@list');
	Route::get('sections/{section}', 'sectionController@get');
	Route::get('locations/', 'locationController@list');
	Route::get('locations/{location}', 'locationController@get');
	Route::get('pois/', 'poiController@list');
	Route::get('pois/{poi}', 'poiController@get');

	Route::group(['middleware' => ['auth:api']], function() {
		Route::get('/user', function(Request $request) {
			return new User($request->user());
		})->middleware('verified');

		Route::get('user/trips', 'UserController@listTrips');

		Route::group(['prefix' => 'trips'], function() {
			Route::post('/', 'tripController@store');
			Route::patch('/{trip}', 'tripController@update');
			Route::delete('/{trip}', 'tripController@destroy');

			Route::post('/{trip}/relationships/members', 'tripController@addMembers');
			Route::delete('/{trip}/relationships/members', 'tripController@removeMembers');
			Route::post('/{trip}/relationships/members/accept', 'tripController@acceptInvite');
			Route::post('/{trip}/relationships/members/decline', 'tripController@declineInvite');
		});

		Route::group(['prefix' => 'reports'], function() {
			Route::post('/', 'reportController@store');
			Route::patch('/{report}', 'reportController@update');
			Route::delete('/{report}', 'reportController@destroy');
		});

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

Route::any('{all}', function() {
	return response()->json([
		"errors" => [
			[
				"status" => "404",
				"title" => "Page not found.",
			]
		],
	], 404);
})->where('all', '.*')->fallback();
