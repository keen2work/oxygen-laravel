<?php

namespace Tests\Feature\API\V1;

use EMedia\Devices\Auth\DeviceAuthenticator;
use EMedia\TestKit\Traits\InteractsWithUsers;
use Tests\TestCase;

class APIBaseTestCase extends TestCase
{

	use InteractsWithUsers;

	/**
	 * @return mixed
	 * @throws RuntimeException
	 */
	protected function getApiKey()
	{
		$key = env('API_KEY', false);

		if (!$key) {
			throw new \RuntimeException("You don't have an active API_KEY on `.env` file.");
		}

		return $key;
	}


	/**
	 *
	 * Return an access token for a user by email
	 *
	 * @param null $email
	 *
	 * @return mixed
	 */
	protected function getAccessToken($email = null)
	{
		$email = $email ?? $this->getDefaultEMail();

		$user = $this->findUserByEmail($email);

		$accessToken = DeviceAuthenticator::getAnAccessTokenForUserId($user->id);

		return $accessToken;
	}

	/**
	 *
	 * Return the default user email
	 *
	 * @return string
	 */
	protected function getDefaultEmail(): string
	{
		return 'apps+user@elegantmedia.com.au';
	}
}
