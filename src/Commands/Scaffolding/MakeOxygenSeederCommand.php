<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

use EMedia\Oxygen\Traits\TransformsEntityNames;
use Illuminate\Support\Str;

class MakeOxygenSeederCommand extends BaseScaffoldCommand
{

	use TransformsEntityNames;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:seeder {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold an oxygen seeder';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Seeder';

	/**
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/Seeders/seeder.stub';
	}

	protected function transformClassName($name)
	{
		return Str::studly(Str::plural($name));
	}

	protected function addClassNameSuffix($name)
	{
		return $this->getPluralStudlyCase($name) . 'Seeder';
	}

	/**
	 * Get the destination class path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getPath($name)
	{
		$name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

		if (is_dir($this->laravel->databasePath().'/seeds')) {
			return $this->laravel->databasePath().'/seeds/'.$name.'.php';
		}

		return $this->laravel->databasePath().'/seeders/'.$name.'.php';
	}

	/**
	 * Get the root namespace for the class.
	 *
	 * @return string
	 */
	protected function rootNamespace()
	{
		return 'Database\Seeders\\';
	}

}
