<?php
namespace Database\Seeders\Auth;

use ElegantMedia\OxygenFoundation\Database\Seeders\SeedWithoutDuplicates;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

	use SeedWithoutDuplicates;

	public function run()
	{
		$this->seedRoles();
	}

	public function seedRoles()
	{
		$defaultRoles = [
			// This is the default Role order
			// Keep this default order and don't put admin or sysadmin as the first role.
			[
				'title' 		=> 'Users',
				'description' 	=> 'Regular Users of the System',
				'assign_by_default'   => true,
				'allow_to_be_deleted' => false,
			],
			[
				'title' 		=> 'Admins',
				'description' 	=> 'Admins of the system (For Clients)',
				'allow_to_be_deleted' => false,
			],
			[
				'title' 		=> 'Super-Admins',
				'description' 	=> 'Super Admins of the system (For Developer/SysAdmin Use)',
				'allow_to_be_deleted' => false,
			],
			[
				'title' 		=> 'Developers',
				'description' 	=> 'For Developer use and system admin tasks.',
				'allow_to_be_deleted' => false,
			],
			[
				'title' 		=> 'Developers',
				'description' 	=> 'For Developer use and system admin tasks.',
				'allow_to_be_deleted' => false,
			],
			[
				'title' 		=> 'Managers',
				'description' 	=> 'For custom business use. Can be deleted if unused.',
				'allow_to_be_deleted' => true,
			],
		];

		$roleModel = config('oxygen.roleModel');
		if (TenantManager::multiTenancyIsActive())
		{
			$tenant = app(config('auth.tenantModel'))->find(1);
			TenantManager::setTenant($tenant);
		}
		$this->seedWithoutDuplicates($defaultRoles, $roleModel, 'title', 'name');

	}

	protected function appendCustomFields($entityModel, $entityData)
	{
		if (isset($entityData['allow_to_be_deleted'])) {
			$entityModel->allow_to_be_deleted = $entityData['allow_to_be_deleted'];
			$entityModel->save();
		}

		if (isset($entityData['assign_by_default'])) {
			$entityModel->assign_by_default = $entityData['assign_by_default'];
			$entityModel->save();
		}
	}
}
