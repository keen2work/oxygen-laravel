<?php
namespace Database\Seeders\Auth;

use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		if (app()->environment() !== 'production') {
			$this->seedTestUsers();
			$this->seedRegularUsers();
			$this->seedEmailVerifications();
		}
	}

	public function seedTestUsers()
	{
		$userModel = app('oxygen')::makeUserModel();

		$users = [
			[
				'name'	 => 'Peter Parker (REGULAR USER)',
				'email'	 => 'apps+user@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
			[
				'name'	 => 'Bruce Banner (ADMIN)',
				'email'	 => 'apps+admin@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
			[
				'name'	 => 'Steve Rogers (SUPER-ADMIN)',
				'email'	 => 'apps@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
			[
				'name'	 => 'Tony Stark (SUPER-ADMIN)',
				'email'	 => 'apps+suadmin@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
			[
				'name'	 => 'Bruce Wayne (DEVELOPER)',
				'email'	 => 'apps+dev@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
			[
				'name'	 => 'Ultron Unverified (REGULAR USER - UNVERIFIED)',
				'email'	 => 'apps+unverified1@elegantmedia.com.au',
				'password' => bcrypt('12345678')
			],
		];

		$i = 0;
		foreach ($users as $key => $data) {
			if (!$user = $userModel::where('email', $data['email'])->first()) {
				$user = $userModel::create($data);

				if (TenantManager::multiTenancyIsActive()) {
					$tenant = app(config('auth.tenantModel'))->find($i + 1);
					TenantManager::setTenant($tenant);
					$user->tenants()->save($tenant);
				}
			}
			$i++;
		}
	}

	public function seedRegularUsers()
	{
		$faker = Faker::create('en_AU');

		$userModel = app('oxygen')::makeUserModel();

		foreach(range(1, 5) as $index)
		{
			$user = $userModel::create([
				'name' => $faker->firstName,
				'last_name' => $faker->lastName,
				'email' => $faker->email,
				'password' => bcrypt('12345678'),
			]);

			$user->assign('users');
		}
	}

	protected function seedEmailVerifications()
	{
		$userModel = app('oxygen')::makeUserModel();

		$users = $userModel::whereIn('email', [
			'apps@elegantmedia.com.au',
			'apps+user@elegantmedia.com.au',
			'apps+admin@elegantmedia.com.au',
			'apps+dev@elegantmedia.com.au',
		])->get();

		foreach ($users as $user) {
			$user->email_verified_at = now();
			$user->save();
		}
	}

}
