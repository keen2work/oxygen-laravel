<?php

namespace Database\Seeders\Auth;

use App\Entities\Auth\AbilityCategory;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Database\Seeder;

class AbilitiesTableSeeder extends Seeder
{

	public function run() {
		$this->seedAbilitiesFromCategories();
	}

	/**
	 *
	 * We don't keep a list of separate abilities. Instead we get all allowed abilities from the `AbilityCategories`
	 * So that means, if you change an ability name (on the related category) after you run the seeder,
	 * It will create a duplicate.
	 *
	 */
	public function seedAbilitiesFromCategories(): void {
		$categories = AbilityCategory::all();

		if (TenantManager::multiTenancyIsActive()) {
			$tenant = app(config('auth.tenantModel'))->find(1);
			TenantManager::setTenant($tenant);
		}

		foreach ($categories as $category) {
			$abilities = json_decode($category->default_abilities);

			foreach ($abilities as $abilityName) {
				$ability = app(config('oxygen.abilityModel'));
				$ability->title = $abilityName;
				$ability->ability_category_id = $category->id;

				$existingAbility = app(config('oxygen.abilityModel'))
					->where('title', $abilityName)
					->where('ability_category_id', $category->id)
					->first()
				;

				if ($existingAbility) {
					// leave it alone
				} else {
					if (TenantManager::multiTenancyIsActive()) {
						$tenant = TenantManager::getTenant();
						$ability->tenant()->associate($tenant);
					}
					$ability->save();
				}
			}
		}
	}

}
