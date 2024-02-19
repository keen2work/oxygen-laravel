<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

use EMedia\Oxygen\Traits\TransformsEntityNames;
use Illuminate\Console\Command;

class MakeOxygenSeedersCommand extends Command
{

	use TransformsEntityNames;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:seeders {name* : The name of the model.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create multiple seeders for models';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:oxygen:seeder', [
				'name' => $arg,
			]);
		}
	}

}
