<?php


namespace EMedia\Oxygen\Http\Middleware;

use Closure;
use ElegantMedia\OxygenFoundation\Exceptions\UserNotFoundException;
use EMedia\Devices\Auth\DeviceAuthenticator;
use EMedia\Devices\Exceptions\DeviceNotFoundException;

class ApiUserAccessTokenVerification
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$accessToken = $request->header('x-access-token');

		if (empty($accessToken)) {
			return response()->apiErrorAccessDenied('x-access-token missing from request');
		}

		// find the user for the given `x-access-token`
		try {
			$user = DeviceAuthenticator::getUserByAccessToken($accessToken);
		} catch (\InvalidArgumentException $ex) {
			return response()->apiErrorUnauthorized('Invalid access token. Try logging in again.');
		} catch (DeviceNotFoundException $ex) {
			return response()->apiErrorUnauthorized('Invalid device access token. Try logging in again.');
		} catch (UserNotFoundException $ex) {
			return response()->apiErrorUnauthorized('Account was deleted or removed. Try logging in again.');
		} catch (\Exception $ex) {
			return response()
				->apiErrorUnauthorized('Access failed due to invalid or expired token. Try logging in again.');
		}

		// At this point, an existing $user is guaranteed
		// Otherwise, a UserNotFoundException must've been thrown.

		$request->setUserResolver(function () use ($user) {
			return $user;
		});

		return $next($request);
	}
}
