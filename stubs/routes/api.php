<?php

// Start Oxygen API routes
Route::group([
	'prefix' => 'v1',
	'middleware' => ['auth.api'],
	'namespace' => '\App\Http\Controllers\API\V1'
], function () {

	if (config('features.api_active')) {
		Route::post('/register', 'Auth\AuthController@register');
		Route::post('/login', 'Auth\AuthController@login');
		Route::post('/password/email', 'Auth\ForgotPasswordController@checkRequest');

		// guest (all-users) API routes
		Route::get('/guests', 'GuestController@index');

		// logged-in users
		Route::group(['middleware' => ['auth.api.logged-in']], function () {
			Route::get('/logout', 'Auth\AuthController@logout');
			Route::get('/profile', 'Auth\ProfileController@index');
			Route::put('/profile', 'Auth\ProfileController@update');
			Route::post('/avatar', 'Auth\ProfileController@updateAvatar');
			Route::post('/password/edit', 'Auth\ResetPasswordController@updatePassword');

			// TODO: add other logged-in user routes
		});
	}
});
// End Oxygen API routes
