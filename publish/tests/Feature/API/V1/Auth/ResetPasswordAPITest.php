<?php


namespace Tests\Feature\API\V1\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

class ResetPasswordAPITest extends \Tests\Feature\API\V1\APIBaseTestCase
{

	use DatabaseTransactions;

	public function testResetPasswordAPIReturnSuccessd()
	{
		$headers['Accept'] = 'application/json';
		$headers['x-access-token'] = $this->getAccessToken();
		$headers['x-api-key'] = $this->getApiKey();

		// form params
		$newPassword = '_12345678';

		$data = [
			'password' => $newPassword,
			'password_confirmation' => $newPassword,
			'current_password' => '12345678',
		];

		$response = $this->post('/api/v1/password/edit', $data, $headers);

		$response->assertStatus(200);

		$user = $this->findUserByEmail($this->getDefaultEmail());
		$this->assertTrue(Hash::check($newPassword, $user->password));
	}
}
