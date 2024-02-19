<?php
namespace EMedia\Oxygen\Database\Seeders;

use EMedia\Oxygen\Database\Seeders\SeedsPermissions;

class PermissionAssigner
{

	use SeedsPermissions;

	protected $roles = [];

	protected $abilityCategories = [];

	protected $excludeAbilities = [];

	protected $includeAbilities = [];

	protected $deleteOldAbilities = false;

	protected $abilityField = 'name';

	protected $roleField = 'name';

	public function reset(): self
	{
		$this->roles = [];

		$this->abilityCategories = [];

		$this->excludeAbilities = [];

		$this->includeAbilities = [];

		$this->deleteOldAbilities = false;

		$this->abilityField = 'name';

		$this->roleField = 'name';

		return $this;
	}

	public function toRole($name): self
	{
		$this->roles[] = $name;

		return $this;
	}

	public function toRoles(array $array): self
	{
		$this->roles = array_merge($this->roles, $array);

		return $this;
	}

	public function category($name): self
	{
		$this->abilityCategories[] = $name;

		return $this;
	}

	public function excludeAbility($name): self
	{
		$this->excludeAbilities[] = $name;

		return $this;
	}

	public function includeAbility($name): self
	{
		$this->includeAbilities[] = $name;

		return $this;
	}

	public function deleteOldAbilities(): self
	{
		$this->deleteOldAbilities = true;

		return $this;
	}

	public function usingAbilityFieldName($name): self
	{
		$this->abilityField = $name;

		return $this;
	}

	public function usingRoleFieldName($name): self
	{
		$this->roleField = $name;

		return $this;
	}

	/**
	 * @throws \Exception
	 */
	public function commit(): void
	{
		$this->validateRoles();

		$this->assignPermissionsToRole(
			$this->roles,
			$this->abilityCategories,
			$this->excludeAbilities,
			$this->includeAbilities,
			$this->deleteOldAbilities,
			$this->abilityField,
			$this->roleField,
		);

		self::reset();
	}

	public function allPermissions(): void
	{
		$this->validateRoles();

		$allAbilities = self::getAbilityModel()::all();

		foreach ($this->roles as $roleName) {
			/** @var \Silber\Bouncer\Database\Role $role */
			$role = self::getRoleModel()::where($this->roleField, $roleName)->first();
			if (!$role) {
				throw new \RuntimeException("Can't find a role named {$roleName}");
			}
			// dd($roleName);
			$role->abilities()->sync($allAbilities, $this->deleteOldAbilities);
		}

		self::reset();
	}

	protected function validateRoles(): void
	{
		if (empty($this->roles)) {
			throw new \RuntimeException("There are no roles. Set at least 1 valid role before trying again.");
		}
	}
}
