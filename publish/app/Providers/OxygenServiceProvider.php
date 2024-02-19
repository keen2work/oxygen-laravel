<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class OxygenServiceProvider extends ServiceProvider
{

	public function register()
	{
		Fortify::ignoreRoutes();
	}

	public function boot()
	{
		// Set custom models for abilities and roles
		$abilityModel = config('oxygen.abilityModel');
		if ($abilityModel) {
			\Silber\Bouncer\BouncerFacade::useAbilityModel($abilityModel);
		}
		$roleModel = config('oxygen.roleModel');
		if ($roleModel) {
			\Silber\Bouncer\BouncerFacade::useRoleModel($roleModel);
		}

		Fortify::createUsersUsing(CreateNewUser::class);
		Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
		Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
		Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

		Fortify::requestPasswordResetLinkView(function () {
			return view('oxygen::auth.passwords.email');
		});

		Fortify::resetPasswordView(function () {
			return view('oxygen::auth.passwords.reset');
		});

		Fortify::verifyEmailView(function () {
			return view('oxygen::auth.verify-email');
		});

		RateLimiter::for('login', function (Request $request) {
			return Limit::perMinute(5)->by($request->email.$request->ip());
		});

		RateLimiter::for('two-factor', function (Request $request) {
			return Limit::perMinute(5)->by($request->session()->get('login.id'));
		});

		\Illuminate\Pagination\Paginator::useBootstrapFive();
	}
}
