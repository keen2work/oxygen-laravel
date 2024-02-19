<?php


namespace EMedia\Oxygen\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\V1\APIBaseController;
use EMedia\Api\Docs\APICall;
use EMedia\Api\Docs\Param;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Fortify;

class ForgotPasswordController extends APIBaseController
{

	public function checkRequest(Request $request)
	{
		document(function () {
			return (new APICall())->setName('Reset Password')
				->setParams([
					(new Param('email'))->setVariable('{{test_user_email}}'),
				])
				->noDefaultHeaders()
				->setHeaders([
					(new Param('Accept', 'String', '`application/json`'))->setDefaultValue('application/json'),
					(new Param('x-api-key', 'String', 'API Key'))->setDefaultValue('123-123-123-123'),
				])->setErrorExample('{
					"message": "Failed to send password reset email. Ensure your email is correct and try again.",
					"payload": null,
					"result": false
				}', 422);
		});

		$request->validate([Fortify::email() => 'required|email']);

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$status = $this->broker()->sendResetLink(
			$request->only(Fortify::email())
		);

		return $status === Password::RESET_LINK_SENT
			? $this->sendResetLinkResponse($request)
			: $this->sendResetLinkFailedResponse($request);
	}

	/**
	 * Get the response for a successful password reset link.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetLinkResponse(Request $request)
	{
		return response()->apiSuccess('', "A password reset email will be sent to you in a moment.");
	}

	/**
	 * Get the response for a failed password reset link.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetLinkFailedResponse(Request $request)
	{
		return response()->apiError("Failed to send password reset email. Ensure your email is correct and try again.");
	}

	/**
	 * Get the broker to be used during password reset.
	 *
	 * @return \Illuminate\Contracts\Auth\PasswordBroker
	 */
	protected function broker(): PasswordBroker
	{
		return Password::broker(config('fortify.passwords'));
	}
}
