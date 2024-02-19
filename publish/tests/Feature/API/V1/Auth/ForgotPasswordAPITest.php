<?php

namespace Tests\Feature\API\V1\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\API\V1\APIBaseTestCase;

class ForgotPasswordAPITest extends APIBaseTestCase
{

	use DatabaseTransactions;

	public function testForgotPasswordAPIReturnsSuccess()
	{
		$headers['Accept'] = 'application/json';
		$headers['x-api-key'] = $this->getApiKey();

		$apiKey = config('oxygen.api_key');

		$this->assertEquals($headers['x-api-key'], $apiKey);

		// form params
		$data['email'] = 'apps+user@elegantmedia.com.au';

		$response = $this->post('/api/v1/password/email', $data, $headers);

		$response->assertStatus(200);

		$this->assertDatabaseHas('password_reset_tokens', [
			'email' => $data['email'],
		]);
	}
}
