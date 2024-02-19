<?php

namespace App\Http\Controllers\API\V1\Auth;

use EMedia\Api\Docs\Param;
use EMedia\Api\Domain\Postman\PostmanVar;

class AuthController extends \EMedia\Oxygen\Http\Controllers\API\V1\Auth\AuthController
{

	/**
	 *
	 * Fillable parameters when registering a new user
	 * Only add fields that must be auto-filled
	 *
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
	];

	/**
	 *
	 * Validation rules to be enforced when registering.
	 *
	 * @return array
	 */
	protected function getRegistrationValidationRules(): array
	{
		return [
			// 'first_name' => 'required',
			// 'last_name'  => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|confirmed|min:8',
			// 'date_of_birth' => 'required|date',
			// 'phone'       => 'required',
			'device_id' => 'required',
			'device_type' => 'required',
		];
	}

	/**
	 *
	 * These are the parameters for APIDoc
	 *
	 * @return array
	 */
	protected function getRegistrationApiDocParams(): array
	{
		return [
			'device_id|Unique ID of the device|{{$guid}}',
			'device_type|Type of the device `APPLE` or `ANDROID`|example:apple',
			'device_push_token|optional|Unique push token for the device',

			'first_name|First name of user|example:Joe|{{$randomFirstName}}',
			'last_name|Last name of user|example:Johnson|{{$randomLastName}}',

			'email|Email address of user|{{$randomExampleEmail}}',
			'password|Password. Must be at least 8 characters.|{{login_user_pass}}',
			'password_confirmation|Confirm password. Must be at least 8 characters.|{{login_user_pass}}',

			// add other fields as required
			// (new Param(
			//	'phone',
			//	'string',
			//	'Phone number in international format'))
			//	->setExample('+61123456789')
			//	->setVariable(PostmanVar::PHONE),

			// (new Param('date_of_birth', 'string', 'Date of birth in YYYY/MM/DD format'))
			// 	->setExample('1990/10/24'),
		];
	}

	// Add your logic here

}
