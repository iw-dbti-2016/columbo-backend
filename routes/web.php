<?php

use Illuminate\Http\Request;
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

Route::group(['prefix' => 'auth'], function() {
	Auth::routes(['verify' => true]);
});

Route::get('/', function() {
	return view('home');
})->middleware(['guest', 'withoutTokenCookies']);

Route::get('/app/{all?}', function(Request $request) {
	if (! $request->cookie(config('api.jwt_sign_cookie_name')) ||
		! $request->cookie(config('api.jwt_payload_cookie_name'))) {
		if (! $request->user()) {
			$signCookie = Cookie::forget(config('api.jwt_sign_cookie_name'));
	    	$payloadCookie = Cookie::forget(config('api.jwt_payload_cookie_name'));

			return redirect()->route('login')
							->cookie($signCookie)
							->cookie($payloadCookie);
		}

        $token = explode(".", JWTAuth::fromUser($request->user()));

		$signCookie = Cookie::make(config('api.jwt_sign_cookie_name'), $token[2], config('jwt.ttl'), $path=null, $domain=null, $secure=false, $httpOnly=true, $raw=false, $sameSite='strict');
	    $payloadCookie = Cookie::make(config('api.jwt_payload_cookie_name'), $token[0] . '.' . $token[1], config('jwt.ttl'), $path=null, $domain=null, $secure=false, $httpOnly=false, $raw=false, $sameSite='strict');

		return response()
					->view('app')
					->cookie($signCookie)
					->cookie($payloadCookie);
	}

	return response()->view('app');
})->where('all', '.*')->name('app');

Route::get('{all?}', function() {
	abort(404);
})->where('all', '.*')->fallback();
