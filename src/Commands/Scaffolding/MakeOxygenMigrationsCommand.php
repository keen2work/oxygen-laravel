<?php

namespace EMedia\Oxygen\Commands\Scaffolding;

use EMedia\Oxygen\Traits\TransformsEntityNames;

class MakeOxygenMigrationsCommand extends \Illuminate\Console\Command
{

	use TransformsEntityNames;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'make:oxygen:migrations {name* : The name of the model.}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create multiple tables for Oxygen Models';

	public function handle()
	{
		$args = $this->argument('name');

		foreach ($args as $arg) {
			$this->call('make:migration', [
				'name' => 'create_' . $this->getPluralSnakeCase($arg) . '_table',
			]);
		}
	}
}
