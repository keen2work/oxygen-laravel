<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

class MakeOxygenRepositoriesCommand extends \Illuminate\Console\Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:repositories {name* : The name of the repository.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold multiple Oxygen Repositories';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:oxygen:repository', [
				'name' => $arg
			]);
		}
	}

}
