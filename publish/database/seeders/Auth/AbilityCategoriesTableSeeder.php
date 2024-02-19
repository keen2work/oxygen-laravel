<?php
namespace Database\Seeders\Auth;

use App\Entities\Auth\AbilityCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AbilityCategoriesTableSeeder extends Seeder
{

	public function run()
	{
		$this->seedAbilityCategories();
	}

	public function seedAbilityCategories()
	{
		$abilityCategories = [
			// user-management
			[
				'name' => 'User Management',
				'default_abilities' => [
					'View Users',
					'Add Users',
					'Edit Users',
					'Delete Users',
					'Disable Users',
					'Enable Users',
				]
			],

			// permission-management
			[
				'name' => 'Permission Management',
				'default_abilities' => [
					'View Permissions',
					'Add Permissions',
					'Edit Permissions',
					'Delete Permissions',
				]
			],

			// group-management (role-management)
			[
				'name' => 'Group Management',
				'default_abilities' => [
					'View Groups',
					'Add Groups',
					'Edit Groups',
					'Delete Groups',

					'Add Group Users',
					'View Group Users',
					'Edit Group Users',
					'Edit Group Permissions',

					'Invite Group Users',
				]
			],
		];

		$this->seedAbilityCategoriesWithoutDuplicates($abilityCategories);
	}

	protected function seedAbilityCategoriesWithoutDuplicates($abilityCategories): void
	{
		// Seed the items without creating duplicate records
		foreach ($abilityCategories as $entityData) {
			$slug = Str::slug($entityData['name']);
			$category = AbilityCategory::where('slug', $slug)->first();
			if (!$category) {
				$category = new AbilityCategory(['name' => $entityData['name']]);
				$category->default_abilities = json_encode($entityData['default_abilities'], JSON_THROW_ON_ERROR);
			} else {
				$existingAbilities = json_decode($category->default_abilities, true, 512, JSON_THROW_ON_ERROR);
				$mergedAbilities = array_unique(array_merge($entityData['default_abilities'], $existingAbilities));
				$category->default_abilities = json_encode($mergedAbilities);
			}

			if ($category->isDirty()) {
				$category->save();
			}
		}
	}
}
