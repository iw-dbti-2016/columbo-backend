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
	Route::group(['prefix' => 'auth'], function() {
		Route::post('/register', 'Auth\API\RegisterController@register')->name('api.auth.register');
		Route::post('/login', 'Auth\API\LoginController@login')->name('api.auth.login');
		Route::patch('/refresh', 'Auth\APIAuthController@refresh')->name('api.auth.refresh');
		Route::delete('/logout', 'Auth\API\LoginController@logout')->name('api.auth.logout');

		Route::post('/password/email', 'Auth\API\ForgotPasswordController@sendResetLinkEmail')->name('api.auth.forgot-password');
		Route::post('/email/resend', 'Auth\API\VerificationController@resend')->name('api.auth.resend');
	});

	Route::group(['middleware' => ['auth:api']], function() {
		Route::get('/user', function(Request $request) {
			return ["success" => true, "data" => $request->user()];
		})->middleware('verified');

		Route::group(['prefix' => 'user'], function() {
			Route::get('/trips', 'UserController@listTrips');
		});

		Route::group(['prefix' => 'users/{user}'], function() {
			Route::get('/', 'UserController@show');
		});

		Route::group(['prefix' => 'trips'], function() {
			Route::get('/{trip}', 'tripController@get');
			Route::post('/', 'tripController@store');
			Route::patch('/{trip}', 'tripController@update');
			Route::delete('/{trip}', 'tripController@destroy');
		});

		Route::group(['prefix' => 'reports'], function() {
			Route::get('/', 'reportController@list');
			Route::get('/{report}', 'reportController@get');
			Route::post('/', 'reportController@store');
			Route::patch('/{report}', 'reportController@update');
			Route::delete('/{report}', 'reportController@destroy');
		});

		Route::group(['prefix' => 'sections'], function() {
			Route::get('/', 'sectionController@list');
			Route::get('/{section}', 'sectionController@get');
			Route::post('/', 'sectionController@store');
			Route::patch('/{section}', 'sectionController@update');
			Route::delete('/{section}', 'sectionController@destroy');
		});

		Route::group(['prefix' => 'locations'], function() {
			Route::post('/', 'locationController@store');
		});
	});
});

Route::any('{all}', function() {
	return response()->json([
		"success" => false,
		"message" => "Page not Found.",
	], 404);
})->where('all', '.*')->fallback();
