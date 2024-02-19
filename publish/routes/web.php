<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\Common\PagesController;

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

// If there's a DEV_BROWSERSYNC_URL given, use it for the URLs
// this will help to generate consistent URLs with BrowserSync
// * Don't use this on a production environment
// * Don't uncomment unless you understand what this will do
// if (app()->environment('local')) {
//    $domainRoot = env('DEV_BROWSERSYNC_URL', '');
//    if ($domainRoot !== '') \Illuminate\Support\Facades\URL::forceRootUrl($domainRoot);
// }

// Start Oxygen routes

// Home
Route::get('/', function () {
	return view('oxygen::pages.welcome', ['pageTitle' => 'The Awesomeness Starts Here...']);
})->name('home');

// Filler Pages...
Route::get('/privacy-policy', [PagesController::class, 'privacyPolicy'])->name('pages.privacy-policy');
Route::get('/terms-conditions', [PagesController::class, 'termsConditions'])->name('pages.terms-conditions');
Route::get('/faqs', [PagesController::class, 'faqs'])->name('pages.faqs');

// Contact Us...
Route::get('/contact-us', [PagesController::class, 'contactUs'])->name('contact-us');
Route::post('/contact-us', [PagesController::class, 'postContactUs']);

// Add Other Custom Pages Here...


Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
	// Email Verification...
	if (Features::enabled(Features::emailVerification())) {
		Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
			->middleware(['auth'])
			->name('verification.notice');

		Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
			->middleware(['auth', 'signed', 'throttle:6,1'])
			->name('verification.verify');

		Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
			->middleware(['auth', 'throttle:6,1'])
			->name('verification.send');
	}
});

// The middleware order must be web, auth -> if you reverse this order, logins will fail
Route::group(
	[
		'middleware' => ['web'],
		'namespace' => '\\App\\Http\\Controllers'],
	function () {

		/*
		 |-----------------------------------------------------------
		 | Public Routes
		 |-----------------------------------------------------------
		 */
		Route::get('logout', '\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController@destroy')
			->name('logout');

		// Route for File Access
		Route::get('files/{uuid}/{fileName?}', 'Manage\ManageFilesController@publicView')->name('files.show');

		// Registration Routes...
		if (has_feature('auth.public_users_can_register')) {
			Route::get('/register', [RegisteredUserController::class, 'create'])
				->middleware(['guest'])
				->name('register');
			// Route::get( 'register', 'Auth\RegisteredUserController@showRegistrationForm')->name('register');
		}

		// Register by Invitation...
		Route::get('invitations/join/{code}', [
			'as'	=> 'invitations.join',
			'uses'	=> 'Auth\InvitationsController@showJoin'
		]);

		// Registration...
		// if (Features::enabled(Features::registration())) {
		// 			Route::post('/register', [RegisteredUserController::class, 'store'])
		// 		->middleware(['guest']);
		// }

		// The registration POST route needs to be open for regular and invitations
		Route::post('register', 'Auth\RegisteredUserController@store')->name('register.store');
	}
);
// End Oxygen routes
