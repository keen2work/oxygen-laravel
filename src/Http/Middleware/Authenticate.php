<?php

namespace EMedia\Oxygen\Http\Middleware;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{


	/**
	 * Determine if the user is logged in to any of the given guards.
	 *
	 * @param Request $request
	 * @param array $guards
	 *
	 * @return void
	 *
	 * @throws AuthenticationException
	 */
	protected function authenticate($request, array $guards)
	{
		if (empty($guards)) {
			$guards = [null];
		}

		foreach ($guards as $guard) {
			if ($this->auth->guard($guard)->check()) {
				return $this->auth->shouldUse($guard);
			}
		}

		// if you reach here, you're still a guest
		// so we check if the admin_security is disabled to allow auto-login
		if (config('oxygen.disable_dashboard_security') === true) {
			if ($this->auth->guest() && app()->environment(['local', 'testing'])) {
				try {
					$loggedUser = $this->auth->loginUsingId(env('DASHBOARD_TEST_LOGIN_USER_ID', 3));
					if ($loggedUser) {
						// you need to share the $user here, because this will be called after
						// LoadViewSettings Middleware
						view()->share('user', $loggedUser);
						return;
					}
				} catch (Exception $ex) {
					throw new Exception('Unable to login. Are there users in the database?');
				}
			}
		}

		$this->unauthenticated($request, $guards);
	}

	/**
	 * Handle an unauthenticated user.
	 *
	 * @param Request $request
	 * @param array $guards
	 *
	 * @return void
	 *
	 * @throws AuthenticationException
	 */
	protected function unauthenticated($request, array $guards)
	{
		if ($request->ajax() || $request->wantsJson()) {
			return response()->apiErrorUnauthorized(
				'You need to login to access this data.' .
				'If you logged-in already, your session may have been expired. Please try to login again.'
			);
		} else {
			throw new AuthenticationException(
				'Unauthenticated.',
				$guards,
				$this->redirectTo($request)
			);
		}
	}
}
