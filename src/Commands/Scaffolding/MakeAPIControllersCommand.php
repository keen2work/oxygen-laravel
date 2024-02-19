<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

class MakeAPIControllersCommand extends \Illuminate\Console\Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:api-controllers {name* : The name of the api-controller.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold multiple API Controllers';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:oxygen:api-controller', [
				'name' => $arg
			]);
		}
	}

}
