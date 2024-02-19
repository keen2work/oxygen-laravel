<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use EMedia\MultiTenant\Facades\TenantManager;
use EMedia\Oxygen\Database\Seeders\PermissionAssigner;
use EMedia\Oxygen\Database\Seeders\Traits\SeedsPermissions;
use Exception;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

	// use SeedsPermissions;


	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		if (TenantManager::multiTenancyIsActive()) {
			$tenant = app(config('auth.tenantModel'))->find(1);
			TenantManager::setTenant($tenant);
		}

		// super-admin, developers
		$assign = new PermissionAssigner();

		$assign->toRole('super-admins')->allPermissions();
		$assign->toRole('developers')->allPermissions();
		$assign->toRole('admins')->category('user-management')->commit();

		// Add additional role permissions
	}


	// /**
	//  *
	//  * Assign permissions to admins
	//  *
	//  * @throws Exception
	//  */
	// private function assignAdminPermissions() {
	// 	$assign->toRole('admins')
	//
	// 	;
	// }
	//
	// /**
	//  *
	//  * Give all permissions to Super-Admins
	//  *
	//  */
	// private function assignSuperAdminPermissions() {
	// 	$allAbilities = $this->abilityModel->all();
	// 	$adminRoles = $this->roleModel->where('name', 'super-admins')->get();
	//
	// 	foreach ($adminRoles as $adminRole) {
	// 		$adminRole->abilities()->sync($allAbilities, false);
	// 	}
	// }




}
