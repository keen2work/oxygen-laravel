<?php

namespace EMedia\Oxygen\Traits;

use Illuminate\Support\Str;

trait TransformsEntityNames
{

	/**
	 * @param $name
	 *
	 * @return string
	 */
	protected function getSingularSnakeCase($name): string
	{
		return Str::snake(Str::singular($name));
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	protected function getPluralSnakeCase($name): string
	{
		return Str::snake(Str::plural($name));
	}

	protected function getSingularStudlyCase($name): string
	{
		return Str::studly(Str::singular($name));
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	protected function getPluralStudlyCase($name): string
	{
		return Str::studly(Str::plural($name));
	}

}
