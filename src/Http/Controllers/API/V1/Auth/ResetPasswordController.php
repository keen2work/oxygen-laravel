<?php


namespace EMedia\Oxygen\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\V1\APIBaseController;
use EMedia\Api\Docs\APICall;
use EMedia\Api\Docs\Param;
use EMedia\Devices\Auth\DeviceAuthenticator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;

class ResetPasswordController extends APIBaseController
{

	protected $auth;
	protected $passwords;
	protected $apiDocGroup = 'Auth';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;
	}

	/**
	 * Update the current logged-in user's password
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updatePassword(Request $request)
	{
		document(function () {
			return (new APICall())->setGroup($this->apiDocGroup)
			->setName('Update Password')
			->setParams([
				(new Param('password'))->setVariable('{{login_user_pass}}'),
				(new Param('current_password'))->setVariable('{{login_user_pass}}'),
				(new Param('password_confirmation'))->setVariable('{{login_user_pass}}'),
			]);
		});

		$user = DeviceAuthenticator::getUserByAccessToken();

		$this->validate($request, [
			'password'	=> 'required|confirmed|min:8',
			'current_password' => 'required',
		], [
			'password.required' => 'New password field is required.'
		]);

		// validate current password
		$isPasswordValid = $this->auth->attempt([
			'email'		=> $user->email,
			'password'	=> $request->get('current_password')
		]);

		if (!$isPasswordValid) {
			return response()->apiErrorUnauthorized('Current password is incorrect.', 422);
		}

		// set the new password
		$user->password = bcrypt($request->get('password'));
		if (!$user->save()) {
			return response()->apiErrorUnauthorized('Failed to save the new password. Try with another password.');
		}

		// TODO: inform the user their password has been changed

		return response()->apiSuccess('', 'Password successfully updated.');
	}
}
