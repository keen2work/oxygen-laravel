<?php
namespace EMedia\Oxygen\Database\Seeders;

use Exception;
use Illuminate\Database\Eloquent\Collection;

trait SeedsPermissions
{

	protected function getRoleModel()
	{
		return app(config('oxygen.roleModel'));
	}

	protected function getAbilityModel()
	{
		return app(config('oxygen.abilityModel'));
	}

	/**
	 *
	 * Assign given permissions (abilities) to a role and save
	 * Pass the roleNames as a string or an array.
	 *
	 * @param array|string $roleNames
	 * @param array $includeAbilityCategories
	 * @param array $blacklistedAbilityNames
	 * @param array $whitelistedAbilityNames
	 * @param bool $deleteOldEntries
	 * @param string $abilityFieldName
	 * @param string $roleFieldName
	 *
	 * @throws Exception
	 */
	private function assignPermissionsToRole(
		$roleNames,
		$includeAbilityCategories = [],
		$blacklistedAbilityNames = [],
		$whitelistedAbilityNames = [],
		$deleteOldEntries = false,
		$abilityFieldName = 'name',
		$roleFieldName = 'name'
	) {

		$roles = new Collection();
		if (is_array($roleNames)) {
			$roles = $this->getRolesByName($roleNames, $roleFieldName);
		} else {
			$roles->push($this->getRoleByName($roleNames, $roleFieldName));
		}

		$abilities = $this->getAbilities(
			$includeAbilityCategories,
			$blacklistedAbilityNames,
			$whitelistedAbilityNames,
			$abilityFieldName
		);

		foreach ($roles as $role) {
			$role->abilities()->sync($abilities, $deleteOldEntries);
		}
	}

	/**
	 * @param        $roleNames
	 * @param string $fieldName
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function getRolesByName($roleNames, $fieldName = 'name')
	{
		$roles = self::getRoleModel()::whereIn($fieldName, $roleNames)->get();

		if ($roles->isEmpty()) {
			throw new \RuntimeException("Roles not found for the given names " . implode(', ', $roleNames));
		}

		return $roles;
	}

	/**
	 *
	 * Get a role by a given name
	 *
	 * @param $roleName
	 * @param string $fieldName
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function getRoleByName($roleName, $fieldName = 'name')
	{
		$role = self::getRoleModel()::where($fieldName, $roleName)->first();

		if (empty($role)) {
			throw new \RuntimeException("Role not found with the name {$roleName}");
		}

		return $role;
	}

	/**
	 *
	 * Get abilities by a category, exclude some abilities, and whitelist some
	 *
	 * @param array $includeCategoryNames
	 * @param array $blacklistedAbilityNames
	 * @param array $whitelistedAbilityNames
	 * @param string $fieldName
	 *
	 * @return mixed
	 */
	private function getAbilities(
		$includeCategoryNames = [],
		$blacklistedAbilityNames = [],
		$whitelistedAbilityNames = [],
		$fieldName = 'name'
	) {

		$query = self::getAbilityModel()::select();

		if (!is_countable($includeCategoryNames) || count($includeCategoryNames) === 0) {
			$includeCategoryNames = ['PLACEHOLDER_CATEGORY_NAME_FOR_EMPTY_RESULTS'];
		}
		$query->whereHas('category', function ($q) use ($includeCategoryNames) {
			// this will match the `slug` in AbilityCategory Model
			$q->whereIn('slug', $includeCategoryNames);
		});

		// blacklist
		if (is_countable($blacklistedAbilityNames) && count($blacklistedAbilityNames) > 0) {
			$query->whereNotIn($fieldName, $blacklistedAbilityNames);
		}
		$includeAbilities = $query->get();

		$whitelistedAbilities = new Collection();
		if (is_countable($whitelistedAbilityNames) && count($whitelistedAbilityNames) > 0) {
			$whitelistedAbilities = self::getAbilityModel()::whereIn($fieldName, $whitelistedAbilityNames)->get();
		}

		return $includeAbilities->merge($whitelistedAbilities);
	}
}
