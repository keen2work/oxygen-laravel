<?php

namespace EMedia\Oxygen\Commands\Scaffolding;


use Illuminate\Support\Str;

class MakeOxygenModelCommand extends BaseScaffoldCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:model {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold an oxygen model';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Model';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/Entities/model.stub';
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
		return Str::studly($name);
	}

}
