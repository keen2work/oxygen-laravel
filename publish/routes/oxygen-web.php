<?php

// Start Oxygen routes

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {

	// Authentication...
	Route::get('/login', [AuthenticatedSessionController::class, 'create'])
		->middleware(['guest'])
		->name('login');

	$limiter = config('fortify.limiters.login');

	Route::post('/login', [AuthenticatedSessionController::class, 'store'])
		->middleware(array_filter([
			'guest',
			$limiter ? 'throttle:'.$limiter : null,
		]));

	// Email Verification Routes...
	// if (has_feature('auth.email_verification_required')) {
	// 	Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
	// 	Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
	// 	Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
	// }

	Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
		->name('logout');

	// Password Reset...
	if (has_feature('auth.allow_forgot_password_resets')) {
		Route::get('/password/forgot', [PasswordResetLinkController::class, 'create'])
			->middleware(['guest'])
			->name('password.request');

		Route::post('/password/forgot', [PasswordResetLinkController::class, 'store'])
			->middleware(['guest'])
			->name('password.email');

		Route::get('/password/reset/{token}', [NewPasswordController::class, 'create'])
			->middleware(['guest'])
			->name('password.reset');

		Route::post('/password/reset', [NewPasswordController::class, 'store'])
			->middleware(['guest'])
			->name('password.update');
	}

	// User's Profile...
	Route::group(['prefix' => 'account', 'namespace' => '\\App\\Http\\Controllers'], function () {
		Route::get('/profile', 'Auth\ProfileController@getProfile')->name('account.profile');
		Route::put('/profile', 'Auth\ProfileController@updateProfile');
		Route::get('/email/edit', 'Auth\ProfileController@getEmail')->name('account.email');
		Route::put('/email/edit', 'Auth\ProfileController@updateEmail');
		Route::get('/password/edit', 'Auth\ResetPasswordController@editPassword')->name('account.password');
		Route::put('/password/edit', 'Auth\ResetPasswordController@updatePassword');
	});

	// Profile Information...
	// if (Features::enabled(Features::updateProfileInformation())) {
	// 	Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
	// 		->middleware(['auth'])
	// 		->name('user-profile-information.update');
	// }
	//
	// // Passwords...
	// if (Features::enabled(Features::updatePasswords())) {
	// 	Route::put('/user/password', [PasswordController::class, 'update'])
	// 		->middleware(['auth'])
	// 		->name('user-password.update');
	// }
	//
	// // Password Confirmation...
	// Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
	// 	->middleware(['auth'])
	// 	->name('password.confirm');
	//
	// Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
	// 	->middleware(['auth']);
	//
	// Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
	// 	->middleware(['auth'])
	// 	->name('password.confirmation');
	//
	// // Two Factor Authentication...
	// if (Features::enabled(Features::twoFactorAuthentication())) {
	// 	Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
	// 		->middleware(['guest'])
	// 		->name('two-factor.login');
	//
	// 	Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
	// 		->middleware(['guest']);
	//
	// 	$twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
	// 		? ['auth', 'password.confirm']
	// 		: ['auth'];
	//
	// 	Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
	// 		->middleware($twoFactorMiddleware);
	//
	// 	Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
	// 		->middleware($twoFactorMiddleware);
	//
	// 	Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
	// 		->middleware($twoFactorMiddleware);
	//
	// 	Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
	// 		->middleware($twoFactorMiddleware);
	//
	// 	Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
	// 		->middleware($twoFactorMiddleware);
	// }
});

// The middleware order must be web, auth -> if you reverse this order, logins will fail
Route::group(
	[
		'middleware' => ['web'],
		'namespace' => '\\App\\Http\\Controllers'],
	function () {

		/*
		 |-----------------------------------------------------------
		 | Admin Dashboard
		 |-----------------------------------------------------------
		 */
		Route::group(['middleware' => ['auth', 'auth.acl:roles[super-admins|admins|developers]']], function () {

			// Dashboard
			Route::get('/dashboard', 'Manage\\DashboardController@dashboard')->name('dashboard');


			Route::group(['prefix' => 'manage', 'as' => 'manage.'], function () {
				// Manage Files
				Route::get('files/view/{uuid}', 'Manage\ManageFilesController@show')->name('files.show');
				Route::get('files/download/{uuid}', 'Manage\ManageFilesController@download')->name('files.download');
				Route::resource('files', 'Manage\ManageFilesController')
					->only('index', 'create', 'store', 'edit', 'update', 'destroy');

				// Manage Documentation
				Route::get('/docs/api', 'Manage\ManageDocumentationController@index')->name('documentation.index');
			});

			// Manage (Super Admin)...
			Route::group(['prefix' => 'account'], function () {
				Route::get('/access', 'Auth\Admin\AccessController@index')->name('manage.access.index');

				// Groups (Roles)
				Route::group(['prefix' => 'groups'], function () {
					Route::get('/', 'Auth\Groups\GroupsController@index')->name('access.groups.index');
					Route::get('/new', 'Auth\Groups\GroupsController@create');
					Route::post('/', 'Auth\Groups\GroupsController@store');
					Route::post('/users', 'Auth\Groups\GroupsController@storeUsers');
					Route::get('{id}/users', 'Auth\Groups\GroupsController@showUsers');
					Route::delete('/{roleId}/users/{userId}', 'Auth\Groups\GroupsController@destroyUser');
					Route::get('/{id}/edit', 'Auth\Groups\GroupsController@edit');
					Route::put('/{id}', 'Auth\Groups\GroupsController@update');
					Route::delete('/{id}', 'Auth\Groups\GroupsController@destroy');

					// Permissions for Role
					Route::group(['prefix' => '{roleId}/permissions'], function () {
						Route::get('/', 'Auth\Abilities\AbilitiesController@editRoleAbilities');
						Route::put('/', 'Auth\Abilities\AbilitiesController@updateRoleAbilities');
					});
				});

				// Abilities (Permissions)
				Route::group(['prefix' => 'permission-categories'], function () {
					Route::get('/', 'Auth\Abilities\AbilityCategoriesController@index')->name('access.abilities.index');
					/*
					Route::get ('/new', 		'Auth\Abilities\AbilityCategoriesController@create');
					Route::get ('/{id}/edit',	'Auth\Abilities\AbilityCategoriesController@edit');
					Route::post('/', 			'Auth\Abilities\AbilityCategoriesController@store');
					Route::put ('/{id}', 		'Auth\Abilities\AbilityCategoriesController@update');
					Route::delete('/{id}',		'Auth\Abilities\AbilityCategoriesController@destroy');
					*/
				});

				// Invitations
				Route::group(['prefix' => 'invitations'], function () {
					Route::get('/', 'Auth\InvitationsController@index')->name('access.invitations.index');
					Route::get('/create', 'Auth\InvitationsController@create')->name('access.invitations.create');
					Route::post('/', 'Auth\InvitationsController@send');
					Route::delete('/{id}', 'Auth\InvitationsController@destroy');
				});

				// Users
				Route::group([
					'as' => 'manage.',
					'namespace' => '\EMedia\Oxygen\Http\Controllers\Auth',
				], function () {
					Route::resource('users', 'UsersController')->only('index', 'edit', 'update', 'destroy');
					Route::get('users/{user}/edit-password', 'UsersController@editPassword')
						->name('users.edit-password');
					Route::put('users/{user}/edit-password', 'UsersController@updatePassword');
					Route::put('users/{user}/update-disabled', 'UsersController@updateDisabled')
						->name('users.update-disabled');
				});
			});
		});
	}
);
// End Oxygen routes
