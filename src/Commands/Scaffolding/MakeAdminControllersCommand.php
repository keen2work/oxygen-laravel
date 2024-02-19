<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

class MakeAdminControllersCommand extends \Illuminate\Console\Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:admin-controllers {name* : The name of the admin-controller.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold multiple Oxygen Admin Controllers';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:oxygen:admin-controller', [
				'name' => $arg
			]);
		}
	}

}
