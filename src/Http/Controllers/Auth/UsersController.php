<?php


namespace EMedia\Oxygen\Http\Controllers\Auth;

use App\Entities\Auth\UsersRepository;
use App\Http\Controllers\Controller;
use App\User;
use EMedia\Formation\Builder\Formation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{

	/**
	 * @var UsersRepository
	 */
	protected $usersRepo;

	protected $indexRoute;

	public function __construct(UsersRepository $usersRepo)
	{
		$this->usersRepo = $usersRepo;

		$this->indexRoute = route('manage.users.index');
	}

	/**
	 *
	 * Show all users
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('oxygen::manage.users.index', [
			'pageTitle' => 'Manage Users',
			'allItems' => $this->usersRepo->searchPaginate(),
		]);
	}

	/**
	 *
	 * Don't create users
	 *
	 */
	public function create()
	{
		// Don't Create Users from Here.
		// If users needs to be added, use the 'Invitations' feature, or let them register.
	}

	/**
	 *
	 * Don't store users
	 *
	 */
	public function store(Request $request)
	{
		// Don't Create Users from Here.
		// If users needs to be added, use the 'Invitations' feature, or let them register.
	}


	/**
	 *
	 * Edit a user
	 *
	 * @param $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($id): \Illuminate\View\View
	{
		$user = $this->usersRepo->find($id);
		if (!$user) {
			abort(404);
		}

		return view('oxygen::manage.users.edit', [
			'pageTitle' => "Edit User - `{$user->full_name}`",
			'entity' => $user,
			'form' => new Formation($user),
		]);
	}


	/**
	 *
	 * Update a user
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param $userId
	 *
	 * @return \Illuminate\Http\Response
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, string $userId)
	{
		$user = $this->usersRepo->find($userId);

		if (!$user) {
			abort(404);
		}

		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $user->id,
		]);

		$data = $request->only(['name', 'last_name', 'email']);

		$user->update($data);

		return redirect($this->indexRoute)->with('success', 'User updated.');
	}


	/**
	 *
	 * Show edit password form
	 *
	 * @param string $userId
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editPassword(string $userId)
	{
		$user = $this->usersRepo->find($userId);

		if (!$user) {
			abort(404);
		}

		return view('oxygen::manage.users.edit-password', [
			'pageTitle' => "Edit Password for `{$user->full_name}`",
			'entity' => $user,
			'actionUrl' => route('manage.users.edit-password', $user->id)
		]);
	}


	/**
	 *
	 * Update the another user's password
	 *
	 * @param $id
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function updatePassword($id, Request $request)
	{
		$user = $this->usersRepo->find($id);
		if (!$user) {
			abort(404);
		}

		$this->validate($request, [
			'password'	=> 'required|confirmed|min:8',
			'current_password' => 'required',
		], [
			'password.required' => 'New password field is required.'
		]);

		// validate current password
		$admin = auth()->user();
		$isPasswordValid = auth()->attempt([
			'email'		=> $admin->email,
			'password'	=> $request->get('current_password')
		]);

		if (!$isPasswordValid) {
			return redirect()->back()->with('error', 'Your current password is incorrect.');
		}

		// set the new password
		$user->password = bcrypt($request->get('password'));
		$user->setRememberToken(null);
		if (!$user->save()) {
			return redirect()->back()->with('error', 'Failed to save the new password. Try with another password.');
		}

		$this->resetDeviceAccessTokensByUser($user);

		return redirect()->back()->with('success', 'Password successfully updated.');
	}

	/**
	 *
	 * If there are devices attached to this user, reset their access tokens, so the user will be forced to login again.
	 *
	 * @param $id
	 */
	protected function resetDeviceAccessTokensByUser(Model $user): void
	{
		if (class_exists('\EMedia\Devices\Entities\Devices\DevicesRepository', false)) {
			/** @var \EMedia\Devices\Entities\Devices\DevicesRepository $devicesRepo */
			$devicesRepo = app('\EMedia\Devices\Entities\Devices\DevicesRepository');
			$devicesRepo->resetAccessTokensByUserId($user->id);
		}
	}

	/**
	 *
	 * Toggle the disabled/enabled status
	 *
	 * @param $id
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function updateDisabled($id, Request $request): RedirectResponse
	{
		$user = $this->usersRepo->find($id);
		if (!$user) {
			abort(404);
		}

		if ($request->action === 'enable') {
			return $this->enable($user);
		}

		return $this->disable($user);
	}

	/**
	 *
	 * Disable a user
	 *
	 * @param Model $user
	 *
	 * @return RedirectResponse
	 */
	protected function disable(Model $user): RedirectResponse
	{
		if ($user->id === auth()->id()) {
			return back()->with('error', 'You cannot disable your own account.');
		}

		if ($user->isDisabled()) {
			return back()->with('error', 'Account is already disabled');
		}

		$user->disable();
		$this->resetDeviceAccessTokensByUser($user);

		return back()->with('success', 'Account disabled.');
	}

	/**
	 *
	 * Enable a user
	 *
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	protected function enable(Model $user): RedirectResponse
	{
		if ($user->isEnabled()) {
			return back()->with('error', 'Account is already enabled.');
		}

		$user->enable();

		return back()->with('success', 'Account enabled.');
	}

	/**
	 *
	 * Delete a user
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy($id)
	{
		/** @var User $user */
		$user = $this->usersRepo->find($id);
		if (!$user) {
			abort(404);
		}

		if ($user->cant('delete-users')) {
			return back()->with('error', 'You do not have permission to delete users.');
		}

		if ($user->id === auth()->id()) {
			return back()->with('error', 'You cannot delete your own account.');
		}

		$this->resetDeviceAccessTokensByUser($user);

		if ($user->stripPIIDAndDelete()) {
			return redirect($this->indexRoute)->with('success', 'User successfully deleted.');
		}

		return redirect($this->indexRoute)->with('error', 'Something went wrong while deleting user.');
	}
}
