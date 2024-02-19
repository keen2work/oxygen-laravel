<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

class MakeOxygenModelsCommand extends \Illuminate\Console\Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:models {name* : The name of the model.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold multiple Oxygen Models';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:oxygen:model', [
				'name' => $arg
			]);
		}
	}

}
