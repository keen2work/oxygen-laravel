<?php


namespace EMedia\Oxygen\Commands\Scaffolding;


use Illuminate\Support\Str;

class MakeAdminControllerCommand extends BaseScaffoldCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:admin-controller {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold an oxygen admin controller';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Controller';

	/**
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/Controllers/Manage/controller.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace."\Http\\Controllers\\Manage";
	}

	protected function transformClassName($name)
	{
		return Str::studly(Str::plural($name));
	}

	protected function addClassNameSuffix($name)
	{
		return $name . 'Controller';
	}
}
