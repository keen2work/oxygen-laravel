<?php


namespace EMedia\Oxygen\Http\Controllers\API\V1\Auth;

use App\Entities\Auth\UsersRepository;
use App\Http\Controllers\API\V1\APIBaseController;
use EMedia\Api\Docs\APICall;
use EMedia\Api\Docs\Param;
use EMedia\Api\Domain\Postman\PostmanVar;
use EMedia\Devices\Auth\DeviceAuthenticator;
use EMedia\Devices\Entities\Devices\DevicesRepository;
use Illuminate\Http\Request;

class AuthController extends APIBaseController
{

	/**
	 * @var UsersRepository
	 */
	protected $usersRepository;
	/**
	 * @var DevicesRepository
	 */
	protected $devicesRepo;

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
	 * Fillable parameters for devices.
	 *
	 */
	protected $fillableDeviceParams = [
		'device_id', 'device_type', 'device_push_token'
	];

	/**
	 * AuthController constructor.
	 * @param UsersRepository $usersRepository
	 * @param DevicesRepository $devicesRepo
	 */
	public function __construct(UsersRepository $usersRepository, DevicesRepository $devicesRepo)
	{
		$this->usersRepository = $usersRepository;
		$this->devicesRepo = $devicesRepo;
	}


	/**
	 *
	 * Validation rules to be enforced when registering.
	 *
	 * @return array
	 */
	protected function getRegistrationValidationRules(): array
	{
		return [
			'email'      => 'required|email|unique:users,email',
			'password'   => 'required|confirmed|min:8',
		];
	}

	/**
	 * @return array
	 */
	protected function getRegistrationApiDocParams(): array
	{
		return [
			(new Param('device_id', Param::TYPE_STRING, 'Unique ID of the device'))
				->setVariable('{{$randomExampleEmail}}'),
			(new Param('device_type', Param::TYPE_STRING, 'Type of the device `APPLE` or `ANDROID`'))
				->setExample('apple'),
			(new Param('device_push_token', Param::TYPE_STRING, 'Unique push token for the device'))
				->optional(),

			(new Param('first_name', Param::TYPE_STRING, 'First name of user'))
				->setExample('Joe')
				->setVariable(PostmanVar::FIRST_NAME),
			(new Param('last_name', Param::TYPE_STRING, 'Last name of user'))
				->setExample('Johnson')
				->setVariable(PostmanVar::LAST_NAME),
			(new Param('email', Param::TYPE_STRING, 'Email address of user'))
				->setVariable(PostmanVar::EXAMPLE_EMAIL),
			(new Param(
				'password',
				'string',
				'Password. Must be at least 8 characters.'
			))
				->setVariable(PostmanVar::REGISTERED_USER_PASS),
			(new Param(
				'password_confirmation',
				'string',
				'Password confirmation. Repeat the password to confirm.'
			))
				->setVariable(PostmanVar::REGISTERED_USER_PASS),
		];
	}

	/**
	 * @return \Closure
	 */
	protected function getRegisterApiDocumentFunction(): callable
	{
		return function () {
			return (new APICall)
				->setName('Register')
				->setDescription('This endpoint registers a user.' .
				 'If you need to update a profile image, upload the profile image in the' .
				 'background using `/avatar` endpoint.')
				->setParams($this->getRegistrationApiDocParams())
				->setApiKeyHeader()
				->setSuccessObject(app('oxygen')->getUserClass())
				->setErrorExample('{
					"message": "The email must be a valid email address.",
					"payload": {
						"errors": {
							"email": [
								"The email must be a valid email address."
							]
						}
					},
					"result": false
				}', 422);
		};
	}

	/**
	 *
	 * Register a user.
	 *
	 * You probably don't need to duplicate this function.
	 * See the other functions and parameters which can be extended as required.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function register(Request $request)
	{
		document($this->getRegisterApiDocumentFunction());

		$this->validate($request, $this->getRegistrationValidationRules());

		$data = $request->only($this->fillable);
		$data['password'] = bcrypt($request->password);
		$user = $this->usersRepository->create($data);

		$responseData = $user->toArray();
		$deviceData = $request->only($this->fillableDeviceParams);
		$device = $this->devicesRepo->createOrUpdateByIDAndType($deviceData, $user->id);
		$responseData['access_token'] = $device->access_token;

		return response()->apiSuccess($responseData);
	}


	/**
	 *
	 * Login to the API and get access token
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function login(Request $request)
	{
		document(function () {
			return (new APICall())->setName('Login')
				->setParams([
					(new Param('device_id', Param::TYPE_STRING, 'Unique ID of the device'))
						->setVariable(PostmanVar::UUID),
					(new Param('device_type', Param::TYPE_STRING, 'Type of the device `APPLE` or `ANDROID`'))
						->setExample('apple'),
					(new Param(
						'device_push_token',
						Param::TYPE_STRING,
						'Unique push token for the device'
					))->optional(),

					(new Param('email'))->setExample('test@example.com')->setVariable('{{test_user_email}}'),
					(new Param('password'))->setVariable('{{login_user_pass}}'),
				])
				->setApiKeyHeader()
				->setSuccessObject(app('oxygen')->getUserClass())
				;
		});

		$this->validate($request, [
			'device_id' => 'required',
			'device_type' => 'required',
			'email' => 'required|email',
			'password' => 'required',
		]);

		if (!auth()->attempt($request->only('email', 'password'), true)) {
			return response()->apiErrorUnauthorized(trans('auth.failed'));
		}

		$user = auth()->user();
		$response = $user->toArray();
		$device = $this->devicesRepo->findByDeviceForUser($user->id, $request->get('device_id'));

		// return an existing device
		if ($device) {
			// reset the push token and access tokens
			// because someone else could be logging in from the same device
			if ($request->device_push_token && ($device->device_push_token !== $request->device_push_token)) {
				$device->device_push_token = $request->device_push_token;
			}
			$device->refreshAccessToken();
		} else {
			// if this is a new device, create it
			$device = $this->devicesRepo->createOrUpdateByIDAndType(
				$request->only('device_id', 'device_type', 'device_push_token'),
				$user->id
			);
		}

		$response['access_token'] = $device->access_token;

		return response()->apiSuccess($response);
	}

	/**
	 *
	 * Logout from the API
	 *
	 */
	public function logout()
	{
		document(function () {
			return (new APICall)->setName('Logout')
				->setDescription('Logout the user from current device');
		});

		$accessToken = request()->header('X-Access-Token');

		DeviceAuthenticator::clearAccessToken($accessToken);

		return response()->apiSuccess(null, 'Logged out from the account.');
	}

	/**
	 *
	 * Logout all devices from the API
	 *
	 */
	public function logoutAllDevices()
	{
		document(function () {
			return (new APICall)->setName('Logout All Devices')
				->setDescription('Logout the user from all devices');
		});

		$user = DeviceAuthenticator::getUserByAccessToken();

		DeviceAuthenticator::clearAllAccessTokensByUserId($user->id);

		return response()->apiSuccess(null, 'Logged out from all the devices.');
	}
}
