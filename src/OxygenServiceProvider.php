<?php

namespace EMedia\Oxygen;

use EMedia\Oxygen\Commands\CreateNewUserCommand;
use EMedia\Oxygen\Commands\OxygenDashboardInstallCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeAdminControllerCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeAdminControllersCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeAPIControllerCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeAPIControllersCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenMigrationsCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenModelCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenModelsCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenRepositoriesCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenRepositoryCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenSeederCommand;
use EMedia\Oxygen\Commands\Scaffolding\MakeOxygenSeedersCommand;
use EMedia\Oxygen\Commands\Scaffolding\ScaffoldViewsCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class OxygenServiceProvider extends ServiceProvider
{

	public function register()
	{
		if (app()->environment(['local', 'testing'])) {
			$this->commands(OxygenDashboardInstallCommand::class);
			$this->commands(ScaffoldViewsCommand::class);
			$this->commands(MakeOxygenModelCommand::class);
			$this->commands(MakeOxygenRepositoryCommand::class);
			$this->commands(MakeAdminControllerCommand::class);
			$this->commands(MakeAPIControllerCommand::class);
			$this->commands(MakeOxygenSeederCommand::class);

			$this->commands(MakeOxygenModelsCommand::class);
			$this->commands(MakeOxygenRepositoriesCommand::class);
			$this->commands(MakeAdminControllersCommand::class);
			$this->commands(MakeAPIControllersCommand::class);
			$this->commands(MakeOxygenSeedersCommand::class);

			$this->commands(MakeOxygenMigrationsCommand::class);
		}

		$this->commands(CreateNewUserCommand::class);

		$this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'oxygen');
	}

	public function boot()
	{
		// auto-publishing files
		$this->publishes([
			__DIR__ . '/../publish' => base_path(),
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen'),
		], 'oxygen::auto-publish');

		// we're adding `views` publishing twice, because if we need to force it after installation later
		// so having this twice is intentional
		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen'),
		], 'views');

		// publish config
		$this->publishes([
			__DIR__.'/../Stubs/config/oxygen.php' => config_path('oxygen.php'),
			__DIR__.'/../Stubs/config/features.php' => config_path('features.php')
		], 'oxygen-config');

		// publish tests
		$this->publishes([
			__DIR__ . '/../stubs/tests/Browser' => base_path('tests/Browser'),
		], 'dusk-tests');

		// load default views
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'oxygen');

		// publish translations
		$this->publishes([
			__DIR__.'/../resources/lang/' => resource_path('lang/vendor/oxygen'),
		], 'oxygen-trans');

		$this->registerCustomValidators();

		Fortify::viewPrefix('oxygen::auth.');

		Blade::componentNamespace('EMedia\\Oxygen\\View\\Components', 'oxygen');
	}


	/**
	 * Configure publishing for the package.
	 *
	 * @return void
	 */
	protected function configurePublishing()
	{
		if (! $this->app->runningInConsole()) {
			return;
		}
	}

	private function registerCustomValidators()
	{
		// custom validation rules

		// match array count is equal
		// eg: match_count_with:permission::name
		// this will match the array count between both fields
		Validator::extend('match_count_with', function ($attribute, $value, $parameters, $validator) {
			// dd(count($value));
			$otherFieldCount = request()->get($parameters[0]);
			return (count($value) === count($otherFieldCount));
		});

		// custom message
		Validator::replacer('match_count_with', function ($message, $attribute, $rule, $parameters) {
			return "The values given in two array fields don't match.";
		});
	}
}
