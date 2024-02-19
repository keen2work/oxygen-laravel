<?php

namespace Database\Seeders;

use Database\Seeders\Auth\AbilitiesTableSeeder;
use Database\Seeders\Auth\AbilityCategoriesTableSeeder;
use Database\Seeders\Auth\PermissionsTableSeeder;
use Database\Seeders\Auth\RolesTableSeeder;
use Database\Seeders\Auth\UserRolesTableSeeder;
use Database\Seeders\Auth\UsersTableSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		Model::unguard();

		$this->call(AbilityCategoriesTableSeeder::class);
		$this->call(AbilitiesTableSeeder::class);
		$this->call(RolesTableSeeder::class);
		$this->call(PermissionsTableSeeder::class);

		// Add development, testing, staging seeders here.
		if (!app()->environment('production')) {
			$this->call(UsersTableSeeder::class);
			$this->call(UserRolesTableSeeder::class);
		}

		/*
		|-------------------------------------------------------------------------------
		| Add production-safe seeders here. DO NOT ADD HERE IF IT ALTERS EXISTING DATA
		|-------------------------------------------------------------------------------
		*/

		Model::reguard();
	}

}
