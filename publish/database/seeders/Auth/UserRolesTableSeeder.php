<?php
namespace Database\Seeders\Auth;

use App\Entities\Auth\RolesRepository;
use App\Entities\Auth\UsersRepository;
use EMedia\Helpers\Exceptions\Auth\UserNotFoundException;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class UserRolesTableSeeder extends Seeder
{

	/**
	 * @var UsersRepository
	 */
	private $usersRepo;
	private $rolesRepo;

	public function __construct(UsersRepository $usersRepo, RolesRepository $rolesRepo)
	{
		$this->usersRepo = $usersRepo;
		$this->rolesRepo = $rolesRepo;
	}

	public function run()
	{
		if (!app()->environment('production')) {
			$this->seedUserRoles();
		}
	}

	protected function seedUserRoles()
	{
		$this->assignRoleToEmail('apps@elegantmedia.com.au', 'super-admins');

		$this->assignRoleToEmail('apps+admin@elegantmedia.com.au', 'admins');
		$this->assignRoleToEmail('apps+suadmin@elegantmedia.com.au', 'super-admins');
		$this->assignRoleToEmail('apps+dev@elegantmedia.com.au', 'developers');
	}

	/**
	 *
	 * Assign a role to a given user by email
	 *
	 * @param $email
	 * @param $roleName
	 */
	protected function assignRoleToEmail($email, $roleName)
	{
		$user = $this->getUserByEmail($email);
		$role = $this->getRoleByName($roleName);

		if (!$user) {
			throw new \Exception("User with an email `$email` not found.");
		}

		if (!$role) {
			throw new \Exception("Role named `$role` not found.");
		}

		$user->roles()->save($role);
	}

	/**
	 *
	 * Get a user by email
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	protected function getUserByEmail($email)
	{
		$user = $this->usersRepo->findByEmail($email);
		if (!$user) throw new InvalidArgumentException("Unable to find a user with email {$email}.");

		return $user;
	}

	/**
	 *
	 * Get a role by name
	 *
	 * @param $roleName
	 *
	 * @return mixed
	 */
	private function getRoleByName($roleName)
	{
		$role = $this->rolesRepo->findByName($roleName);
		if (!$role) throw new InvalidArgumentException("Unable to find a role with the name {$roleName}.");

		return $role;
	}

}
