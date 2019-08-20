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

Route::middleware('insert-client-secret')->post('/oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->name('passport.token');
Route::post('/oauth/token/refresh', '\Laravel\Passport\Http\Controllers\TransientTokenController@refresh')->name('passport.token.refresh');
Route::middleware('auth:api')->get('/oauth/tokens', '\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@forUser')->name('passport.tokens.index');
Route::middleware('auth:api')->delete('/oauth/tokens/{token_id}', '\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@destroy')->name('passport.tokens.destroy');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
