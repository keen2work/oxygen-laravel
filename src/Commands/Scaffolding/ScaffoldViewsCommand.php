<?php


namespace EMedia\Oxygen\Commands\Scaffolding;

use ElegantMedia\PHPToolkit\Dir;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use Illuminate\View\ViewFinderInterface;

class ScaffoldViewsCommand extends BaseScaffoldCommand
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'scaffold:views {paths?* : View resource path}';


	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scaffold the default views for a resource';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->createViews();
	}

	protected function createViews()
	{
		// get resource path eg: manage.users, oxygen::manage.users

		$paths = $this->argument('paths');

		if (empty($paths)) {
			$answer = $this->ask("What is the view resource path, excluding final view name? (Example: manage.users)");
			if ($answer) {
				$paths[] = $answer;
			}
		}

		if (empty($paths)) {
			$this->error("Need a resource path to continue");
			return;
		}

		foreach ($paths as $path) {
			$this->createViewsForPath($path);
		}
	}

	/**
	 * @param $path
	 *
	 * @return void
	 * @throws FileNotFoundException
	 * @throws \ElegantMedia\PHPToolkit\Exceptions\FIleSystem\DirectoryNotCreatedException
	 */
	protected function createViewsForPath($path)
	{
		$delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

		$vendor = explode($delimiter, $path);
		if (is_countable($vendor) && count($vendor) > 1) {
			$vendorName = $vendor[0];
			$path = $vendor[1];
			$resourcePath = resource_path('views' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $vendorName);
		} else {
			$resourcePath = resource_path('views');
		}

		$directories = explode('.', $path);
		foreach ($directories as $directory) {
			$resourcePath .= DIRECTORY_SEPARATOR . $directory;
		}
		Dir::makeDirectoryIfNotExists($resourcePath);

		// create the files
		foreach ($this->getStubs() as $key => $stub) {
			if (!file_exists($stub)) {
				throw new FileNotFoundException("Required file {$stub} was not found.");
			}

			$destinationFilePath = $resourcePath . DIRECTORY_SEPARATOR . "$key.blade.php";
			if (file_exists($destinationFilePath)) {
				if (!$this->confirm("View {$destinationFilePath} already exists. Overwrite?", false)) {
					$this->error('View already exists. Skipping...');
					continue;
				}
			}

			if (copy($stub, $destinationFilePath)) {
				$this->info("View created - {$destinationFilePath}");
			} else {
				$this->error("Failed to create - {$destinationFilePath}");
			}
		}
	}

	protected function getStubs()
	{
		return [
			'index' => __DIR__ . '/../../../resources/views/defaults/allItems-index.blade.php',
			'form'  => __DIR__ . '/../../../resources/views/defaults/formation-form.blade.php',
			'show'  => __DIR__ . '/../../../resources/views/defaults/show.blade.php',
		];
	}

	protected function getStub()
	{
		// TODO: Implement getStub() method.
	}
}
