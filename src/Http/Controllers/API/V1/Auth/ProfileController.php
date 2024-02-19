<?php

namespace EMedia\Oxygen\Http\Controllers\API\V1\Auth;

use EMedia\Api\Domain\Postman\PostmanVar;
use Storage;
use App\Entities\Auth\UsersRepository;
use App\Http\Controllers\API\V1\APIBaseController;
use EMedia\Api\Docs\APICall;
use EMedia\Api\Docs\Param;
use EMedia\Devices\Auth\DeviceAuthenticator;
use Illuminate\Http\Request;

class ProfileController extends APIBaseController
{

	protected $avatarDiskName = 'public';

	/**
	 * @var UsersRepository
	 */
	protected $usersRepo;

	public function __construct(UsersRepository $usersRepo)
	{
		$this->usersRepo = $usersRepo;
	}

	/**
	 *
	 * Get currently logged-in user's profile
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
	{
		document(function () {
			return (new APICall)->setName('My Profile')
				->setDescription('Get currently logged in user\'s profile')
				->setSuccessObject(app('oxygen')::getUserClass());
		});

		$user = DeviceAuthenticator::getUserByAccessToken();

		return response()->apiSuccess($user);
	}

	/**
	 *
	 * Update user's profile
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request)
	{
		document(function () {
			return (new APICall)
				->setName('Update My Profile')
				->setParams([
					(new Param('first_name'))->setVariable(PostmanVar::FIRST_NAME),
					(new Param('last_name'))->optional(),
					(new Param('email'))->setVariable('{{test_user_email}}'),
					(new Param('phone'))->optional(),
					// (new Param('_method'))->description("Must be set to `PUT`")->setDefaultValue('put'),
				])
				->setSuccessObject(app('oxygen')::getUserClass());
		});

		$user = DeviceAuthenticator::getUserByAccessToken();

		$this->validate($request, [
			'first_name' => 'required',
			'email' => 'required|email|unique:users,email,' . $user->id,
		]);

		$user = $this->usersRepo->update($user, $request->only('first_name', 'last_name', 'email', 'phone'));

		return response()->apiSuccess($user);
	}

	/**
	 *
	 * Update user's profile picture (avatar)
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateAvatar(Request $request)
	{
		document(function () {
			return (new APICall)
				->setName('Update My Avatar')
				->hasFileUploads()
				->setParams([
					(new Param('image'))->dataType('File')->setVariable(PostmanVar::RANDOM_IMAGE_FILE),
				])
				->setSuccessObject(app('oxygen')::getUserClass());
		});

		$user = DeviceAuthenticator::getUserByAccessToken();

		$this->validate($request, [
			'image' => 'file|image|mimes:jpeg,png,gif',
		]);

		// save the file
		if ($request->hasFile('image')) {
			$diskName = $this->avatarDiskName;
			$disk = Storage::disk($diskName);

			$path = $request->image->store('avatars/' . $user->id, $diskName);
			$url = $disk->url($path);

			$user->avatar_path = $path;
			$user->avatar_url  = $url;
			$user->avatar_disk = $diskName;
			$user->save();

			return response()->apiSuccess($user->fresh());
		}

		return response()->apiError('Avatar could not be saved.');
	}
}
