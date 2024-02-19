<?php


namespace EMedia\Oxygen\Http\Controllers\Auth;

use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController extends \Laravel\Fortify\Http\Controllers\RegisteredUserController
{

	/**
	 * Create a new registered user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Laravel\Fortify\Contracts\CreatesNewUsers  $creator
	 * @return \Laravel\Fortify\Contracts\RegisterResponse
	 */
	public function store(
		Request $request,
		CreatesNewUsers $creator
	): RegisterResponse {

		$invitation_code = null;

		// if we have an incoming code, let the user join that team
		$invitationsRepo = app(config('oxygen.invitationRepository'));

		$roleRepo		 = app(config('oxygen.roleRepository'));

		if (!empty($invitation_code = session()->get('invitation_code'))) {
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code, true);
			if (!$invite) {
				return response()
					->redirect()->back()
					->withInput($request->except('password', 'confirm_password'))
					->with(
						'error',
						'The invitation is already used or expired. Please login or register for a new account.'
					);
			}
			if (TenantManager::multiTenancyIsActive()) {
				$tenant = $this->getTenantRepo()->find($invite->tenant_id);
			}
		} else {
			// create a tenant
			if (TenantManager::multiTenancyIsActive()) {
				$tenant = $this->getTenantRepo()->create($request->all());
			}
		}

		if (TenantManager::multiTenancyIsActive()) {
			TenantManager::setTenant($tenant);

			// create a user and attach to tenant
			$user = $creator->create($request->all());
			$tenant->users()->attach($user->id);
		} else {
			$user = $creator->create($request->all());
		}

		// assign this user as the admin of the tenant
		if (!empty($invite->role_id)) {
			$user->roles()->attach($invite->role_id);
			// since the tenant is now set, we can retrieve the correct invitation as Eloquent
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code);
			$invitationsRepo->claim($invite);
			Session::flash('success', trans('oxygen::auth.invite-accepted'));
		} else {
			// add the default Roles
			$defaultRoles = $roleRepo->getAssignByDefaultRoles();
			foreach ($defaultRoles as $role) {
				// create the default roles if they don't exist
				// this can be Seeded for single-tenants, but required in multi-tenancy
				/*$role = $roleRepo->findByName($defaultRole['name']);
				if (!$role) {
					$role = $roleRepo->newModel();
					$role->fill($defaultRole);
					$role->name = $defaultRole['name'];
					$role->save();
				}*/

				// add this role when the user registers
				$user->roles()->attach($role->id);
			}

			// $this->redirectTo = $user->isA(['admin', 'super-admin']) ? '/dashboard' : '/';

			Session::flash('success', trans('oxygen::auth.registration-completed'));
		}

		event(new Registered($user));

		$this->guard->login($user);

		return app(RegisterResponse::class);
	}

	protected function getTenantRepo()
	{
		return app(config('oxygen.tenantRepository'));
	}
}
