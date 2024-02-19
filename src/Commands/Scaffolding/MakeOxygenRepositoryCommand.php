<?php


namespace EMedia\Oxygen\Commands\Scaffolding;


use Illuminate\Support\Str;

class MakeOxygenRepositoryCommand extends BaseScaffoldCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:repository {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold an oxygen repository';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Repository';

	/**
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/Entities/repository.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace."\Entities\\".$this->getEntityPlural();
	}

	protected function transformClassName($name)
	{
		return Str::studly(Str::plural($name));
	}

	protected function addClassNameSuffix($name)
	{
		return $name . 'Repository';
	}
}
