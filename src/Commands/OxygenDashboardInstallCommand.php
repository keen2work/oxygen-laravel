<?php


namespace EMedia\Oxygen\Commands;

use ElegantMedia\OxygenFoundation\Console\Commands\ExtensionInstallCommand;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use EMedia\Oxygen\Installer\OxygenAdminConfig;
use Illuminate\Support\Facades\File;
use Laravel\Fortify\FortifyServiceProvider;

// use Laravel\Fortify\FortifyServiceProvider;

class OxygenDashboardInstallCommand extends ExtensionInstallCommand
{

	protected $signature   = 'oxygen:dashboard:install
								{--confirm : Confirm with the user if there are potential issues}
								{--name= : Name of the project}
								{--dev_url= : Development URL alias of the local machine}
								{--email= : Default email for system emails and seeding}

								{--dbconn= : Database connection}
								{--dbhost= : Database host}
								{--dbport= : Database port}
								{--dbname= : Database name}
								{--dbuser= : Database user}
								{--dbpass= : Database password}

								{--mailer=   : Mailer}
								{--mailhost= : Mail host}
								{--mailport= : Mail port}
								{--mailuser= : Mail username}
								{--mailpass= : Mail password}
								{--mailenc=  : Mail encryption}

								{--stack=oxygen : UI Stack}
								{--teams : Has teams support or not}

								{--public_folder=public_html : Give a new path for the public folder}

								{--install_dependencies=true : Skip installing dependencies}
								';

	protected $description 	= 'Run Oxygen Admin Installer';

	/**
	 *
	 * Add Service Provider Reference
	 *
	 * @return string
	 */
	public function getExtensionServiceProvider(): string
	{
		return \EMedia\Oxygen\OxygenServiceProvider::class;
	}

	/**
	 *
	 * Extension Display Name
	 *
	 * @return string
	 */
	public function getExtensionDisplayName(): string
	{
		return 'Oxygen Admin Extension';
	}

	/**
	 * @var OxygenAdminConfig
	 */
	protected $config;

	protected $progressLog = [
		'info' 		=> [],
		'errors' 	=> [],
		'comments'	=> [],
		'instructions' => [],
		'files' => [],
	];

	protected $composerRequire = [
		// 'laravel/sanctum:^2.6',
	];

	protected $composerRequireDev = [
		'barryvdh/laravel-debugbar:^3.8',
		'laravel/dusk:^7.7',
		'emedia/laravel-test-kit:^3.0'
	];

	protected $composerDontDiscover = [
		// add don't discover packages
	];

	protected $requiredServiceProviders = [
		\App\Providers\OxygenServiceProvider::class,
		\EMedia\MultiTenant\MultiTenantServiceProvider::class,
	];

	protected $requiredNpmPackages = [
		'@fortawesome/fontawesome-free' => '~5.15.4',
		'@popperjs/core' => '^2.10.2',
		'@vitejs/plugin-vue' => '^4.1.0',
		'bootstrap' => '^5.1.3',
		'jquery' => '~3.6.0',
		'jquery-validation' => '~1.19.3',
		'select2' => '~4.1.0-rc.0',
		'dropzone' => '~5.9.3',
		'sweetalert2' => '~11.4.20',
		'vue' => '^3.2.0',
	];

	protected $requiredNpmDevPackages = [
		'sass' => '~1.49.7',
	];

	/**
	 *
	 * Gather info before running setup
	 *
	 */
	public function beforeSetup(): void
	{
		$this->config = new OxygenAdminConfig($this->options());

		$this->getDeveloperInput();
	}

	/**
	 *
	 * Run after publishing assets
	 *
	 * @throws FileNotFoundException
	 */
	protected function afterPublishing(): void
	{
		// need to publish Fortify Assets, because we need the actions.
		// but we won't actually use the service provider itself.
		$this->call('vendor:publish', [
			'--provider' => FortifyServiceProvider::class,
		]);

		// update middleware
		$this->updateMiddleware();

		// we don't ask for confirmation on this
		$this->replaceKnownStrings();

		// update Auth/User Models
		$this->updateAuthModels();

		$this->updateEnvironmentVariables();

		// update dot files
		$this->updateDotFiles();

		// rename `public` folder
		// this should happen after all view changes
		$this->movePublicFolder();
	}

	/**
	 *
	 * Run after setup is completed
	 *
	 */
	protected function afterSetup(): void
	{
		// install the other extensions
		$this->call('oxygen:devices:install');
		$this->call('oxygen:app-settings:install');

		// add user display messages
		$this->progressLog['instructions'][] = ['php artisan db:refresh',
			'Migrate and seed the database'];
		$this->progressLog['instructions'][] = ['npm install',
			'Install NPM packages. Check if Node.js is installed with `npm -v`'];
		$this->progressLog['instructions'][] = ['npm run dev',
			'Run and watch the application on browser (Does NOT work with Homestead)'];
		$this->progressLog['instructions'][] = ['npm run build',
			'Compile and build for production.'];

		// Setup Completed! Show any info to the user.
		$this->showProgressLog();

		$this->updateReadMeFile();
	}


	/**
	 *
	 *	Get user input to customise setup
	 *
	 */
	protected function getDeveloperInput(): void
	{
		$this->config->name = ($this->option('name')) ?? $this->ask('What is the project name?', 'Oxygen Admin');

		$this->config->from_email = ($this->option('email')) ??
			$this->ask(
				'What is the `from` email address for system emails? (Press ENTER key for default)',
				'apps@elegantmedia.com.au'
			);

		$this->config->dev_email = ($this->option('email')) ??
			$this->anticipate(
				'What is your email to seed the database? (Press ENTER key for default)',
				[],
				$this->config->from_email
			);

		// dev machine URL
		$devUrl = 'localhost.test';
		if (!empty($this->config->name)) {
			$devUrl = \Illuminate\Support\Str::kebab($this->config->name) . '.test';
		}

		$this->config->dev_url = ($this->option('dev_url')) ??
			$this->anticipate('What is the local development URL? (Press ENTER key for default)', [], $devUrl);

		$this->config->public_folder = $this->option('public_folder');

		// add a stack
		$this->config->stack = ($this->option('stack')) ??
			$this->choice('Add a UI stack? (Press ENTER key for default)', ['no stack', 'livewire', 'intertia'], 1);

		if ($this->config->stack === 'no stack') {
			$this->config->stack = false;
		}

		// teams
		$this->config->teams = $this->hasOption('teams');

		$this->info($this->config->stack);
	}


	/**
	 *
	 * Update Middleware
	 *
	 * @throws FileNotFoundException
	 */
	public function updateMiddleware(): void
	{
		$filePath = app_path('Http/Kernel.php');

		// Update $routeMiddleware
		if (!FileEditor::isTextInFile($filePath, "AuthorizeAcl")) {
			FileEditor::findAndReplace(
				$filePath,
				"'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,",
				implode(PHP_EOL."\t\t", [
					"'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,",
					"'auth.acl' => \EMedia\Oxygen\Http\Middleware\AuthorizeAcl::class,",
					"'auth.api' => \EMedia\Oxygen\Http\Middleware\ApiAuthenticate::class,",
					"'auth.api.logged-in' => \EMedia\Oxygen\Http\Middleware\ApiUserAccessTokenVerification::class,"
				])
			);
		}

		// if (!FileEditor::isTextInFile($filePath, "Oxygen\Http\Middleware\Authenticate::class")) {
		// 	FileEditor::findAndReplace(
		// 		$filePath,
		// 		"'auth' => \App\Http\Middleware\Authenticate::class,",
		// 		implode(PHP_EOL."\t\t", [
		// 			"'auth' => \EMedia\Oxygen\Http\Middleware\Authenticate::class,"
		// 		])
		// 	);
		// }

		// update `web` middleware
		if (!FileEditor::isTextInFile($filePath, "LoadViewSettings")) {
			FileEditor::findAndReplace(
				$filePath,
				"\App\Http\Middleware\VerifyCsrfToken::class,",
				implode(PHP_EOL."\t\t\t", [
					"\App\Http\Middleware\VerifyCsrfToken::class,",
					"\EMedia\Oxygen\Http\Middleware\LoadViewSettings::class,",
				])
			);
		}

		// update `api` middleware
		if (!FileEditor::isTextInFile($filePath, "ParseNonPostFormData")) {
			FileEditor::findAndReplace(
				$filePath,
				"'throttle:api',",
				implode(PHP_EOL."\t\t\t", [
					"'throttle:api',",
					"\EMedia\Oxygen\Http\Middleware\ParseNonPostFormData::class,",
				])
			);
		}
	}


	public function replaceKnownStrings(): void
	{
		$stringsToReplace = [
			[
				'path'		=> app_path('Providers/RouteServiceProvider.php'),
				'search'	=> "public const HOME = '/home'",
				'replace'	=> "public const HOME = '/dashboard'"
			],
			[
				'path'		=> config_path('fortify.php'),
				'search'	=> "'home' => '/home',",
				'replace'	=> "'home' => '/dashboard',"
			],
			// database settings
			// [
			// 		'path'		=> config_path('database.php'),
			//		'search'	=> "'engine' => null,",
			//		'replace'	=> "'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',",
			// ],
		];

		// Change project name in readme.md
		if (!empty($this->config->name)) {
			$stringsToReplace[] = [
				'path'		=> base_path('readme.md'),
				'search'	=> "OxygenProject",
				'replace'	=> $this->config->name,
			];
		}

		if (!empty($this->config->dev_url)) {
			$stringsToReplace[] = [
				'path'		=> base_path('vite.config.js'),
				'search'	=> "localhost.test",
				'replace'	=> $this->config->dev_url,
			];
		}

		if (!empty($this->config->dev_email)) {
			$stringsToReplace[] = [
				'path'		=> database_path('seeders/Auth/UsersTableSeeder.php'),
				'search'	=> "apps@elegantmedia.com.au",
				'replace'	=> $this->config->dev_email,
			];
		}

		// database settings
		if ($dbhost = $this->config->dbhost) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "DB_HOST=127.0.0.1",
				'replace'	=> "DB_HOST=\"{$dbhost}\"",
			];
		}

		if ($dbport = $this->config->dbport) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "DB_PORT=3306",
				'replace'	=> "DB_PORT=\"{$dbport}\"",
			];
		}

		if ($dbuser = $this->config->dbuser) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "DB_USERNAME=root",
				'replace'	=> "DB_USERNAME=\"{$dbuser}\"",
			];
		}

		if ($dbpass = $this->config->dbpass) {
			// use regex instead of text search, because we need to match line ending
			FileEditor::findAndReplaceRegex(base_path('.env'), '/DB_PASSWORD=\s/', "DB_PASSWORD=\"{$dbpass}\"".PHP_EOL);
		}

		// mail settings
		if ($mailer = $this->option('mailer')) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_MAILER=smtp",
				'replace'	=> "MAIL_MAILER=\"{$mailer}\"",
			];
		}

		if ($mailhost = $this->config->mailhost) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_HOST=smtp.mailtrap.io",
				'replace'	=> "MAIL_HOST=\"{$mailhost}\"",
			];
			// From Laravel v8.5.0, the default mail host changed to 'mailhog'
			// So we have to keep both of these hosts
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_HOST=mailhog",
				'replace'	=> "MAIL_HOST=\"{$mailhost}\"",
			];
			// From Laravel v9.0, the default mail host changed to 'mailpit'
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_HOST=mailpit",
				'replace'	=> "MAIL_HOST=\"{$mailhost}\"",
			];
		}

		if ($mailport = $this->config->mailport) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_PORT=2525",
				'replace'	=> "MAIL_PORT=\"{$mailport}\"",
			];
			// From Laravel v8.5.0, the default mail port changed to '1025'
			// So we have to keep both of these hosts
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_PORT=1025",
				'replace'	=> "MAIL_PORT=\"{$mailport}\"",
			];
		}

		if ($mailuser = $this->config->mailuser) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_USERNAME=null",
				'replace'	=> "MAIL_USERNAME=\"{$mailuser}\"",
			];
		}

		if ($mailpass = $this->config->mailpass) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_PASSWORD=null",
				'replace'	=> "MAIL_PASSWORD=\"{$mailpass}\"",
			];
		}

		if ($mailenc = $this->config->mailenc) {
			$stringsToReplace[] = [
				'path'		=> base_path('.env'),
				'search'	=> "MAIL_ENCRYPTION=null",
				'replace'	=> "MAIL_ENCRYPTION=\"{$mailenc}\"",
			];
		}

		$stringsToReplace[] = [
			'path'		=> app_path('Providers/RouteServiceProvider.php'),
			'search'	=> "// protected \$namespace = 'App\\\\Http\\\\Controllers';",
			'replace'	=> "protected \$namespace = 'App\\Http\\Controllers';",
		];

		$stringsToReplace[] = [
			'path'		=> app_path('Http/Middleware/Authenticate.php'),
			'search'	=> 'use Illuminate\Auth\Middleware\Authenticate as Middleware;',
			'replace'	=> 'use EMedia\Oxygen\Http\Middleware\Authenticate as Middleware;',
		];

		//		if ($this->projectConfig['multiTenant']) {
		//			$stringsToReplace[] = [
		//				'path'		=> config_path('oxygen.php'),
		//				'search'	=> "'multiTenantActive' => false,",
		//				'replace'	=> "'multiTenantActive' => true,"
		//			];
		//			$stringsToReplace[] = [
		//				'path'		=> app_path('Entities/Auth/Ability.php'),
		//				'search'	=> "use EMedia\Oxygen\Entities\Auth\SingleTenant\Ability as AbilityBase;",
		//				'replace'	=> "use EMedia\Oxygen\Entities\Auth\MultiTenant\Ability as AbilityBase;"
		//			];
		//			$stringsToReplace[] = [
		//				'path'		=> app_path('Entities/Auth/Role.php'),
		//				'search'	=> "use EMedia\Oxygen\Entities\Auth\SingleTenant\Role as BaseRole;",
		//				'replace'	=> "use EMedia\Oxygen\Entities\Auth\MultiTenant\Role as BaseRole;"
		//			];
		//		}


		// common settings for .env, .env.example
		foreach ([base_path('.env'), base_path('.env.example')] as $filePath) {
			if ($this->config->name) {
				$stringsToReplace[] = [
					'path' => $filePath,
					'search' => "APP_NAME=Laravel",
					'replace' => "APP_NAME=\"{$this->config->name}\"",
				];
			}

			$stringsToReplace[] = [
				'path'    => $filePath,
				'search'  => "MAIL_FROM_ADDRESS=hello@example.com",
				'replace' => "MAIL_FROM_ADDRESS=\"{$this->config->email}\"",
			];

			$stringsToReplace[] = [
				'path'    => $filePath,
				'search'  => "MAIL_FROM_NAME=\"\${APP_NAME}\"",
				'replace' => "MAIL_FROM_NAME=\"\${APP_NAME} (DEV)\"",
			];

			if ($this->config->dev_url) {
				$stringsToReplace[] = [
					'path' 		=> $filePath,
					'search'	=> "APP_URL=http://localhost",
					'replace'	=> "APP_URL=http://{$this->config->dev_url}",
				];
			}

			if ($dbconn = $this->config->dbconn) {
				$stringsToReplace[] = [
					'path'		=> $filePath,
					'search'	=> "DB_CONNECTION=mysql",
					'replace'	=> "DB_CONNECTION=\"{$dbconn}\"",
				];
			}

			if ($dbname = $this->config->dbname) {
				$stringsToReplace[] = [
					'path'		=> $filePath,
					'search'	=> "DB_DATABASE=laravel",
					'replace'	=> "DB_DATABASE=\"{$dbname}\"",
				];
			}
		}

		foreach ($stringsToReplace as $stringData) {
			$this->replaceIn($stringData['path'], $stringData['search'], $stringData['replace']);
		}
	}


	protected function updateAuthModels(): void
	{
		$source = __DIR__.'/../../stubs/app/Models/User.SingleTenant.php';
		$original = __DIR__.'/../../laravel/laravel/app/Models/User.php';

		$target = app_path('Models/User.php');

		if (FileEditor::isTextInFile($target, 'OxygenUserTrait')) {
			return;
		}

		if (!FileEditor::areFilesSimilar($original, $target)) {
			if (!$this->confirm("Seems `{$target}` file is modified. Overwrite?", false)) {
				return;
			}
		}

		$this->info("Updating `{$target}`...");
		File::copy($source, $target);
	}


	/**
	 *
	 * Update .env variables
	 *
	 * @throws FileNotFoundException
	 */
	protected function updateEnvironmentVariables(): void
	{
		if (!FileEditor::isTextInFile(base_path('.env'), 'Oxygen Settings')) {
			$stub = __DIR__.'/../../stubs/.env.oxygen.stub';
			if (!file_exists($stub)) {
				throw new FileNotFoundException("File $stub not found.");
			}

			$contents = file_get_contents($stub);

			File::append(base_path('.env'), $contents);
			File::append(base_path('.env.example'), $contents);
		}
	}


	/**
	 *
	 * Update dot files and similar config
	 *
	 * @throws FileNotFoundException
	 */
	protected function updateDotFiles(): void
	{
		if (!file_exists(base_path('.gitignore'))) {
			File::copy(__DIR__.'/../../stubs/.gitignore', base_path('.gitignore'));
		} else {
			$file = base_path('.gitignore');

			if (!FileEditor::isTextInFile($file, '.idea')) {
				File::append($file, "\r\n/.idea");
			}

			if (!FileEditor::isTextInFile($file, 'debugbar')) {
				File::append($file, "\r\n/storage/debugbar");
			}
		}
	}

	/**
	 *
	 * Move the `public` folder
	 *
	 */
	public function movePublicFolder(): void
	{
		if ($this->config->public_folder) {
			// move public folder to public_html
			$this->call('oxygen:foundation:move-public', ['destination' => $this->config->public_folder]);
		}
	}


	/**
	 *
	 * Show Progress Log
	 *
	 */
	protected function showProgressLog(): void
	{
		$this->info('');
		$this->info('***** SUMMARY *****');
		$this->info('');


		if (is_countable($this->progressLog['files']) && count($this->progressLog['files'])) {
			$this->info('Check these files for accuracy.');

			$headers = ['File', 'What you should check'];
			$this->table($headers, $this->progressLog['files']);
		}

		if (is_countable($this->progressLog['instructions']) && count($this->progressLog['instructions'])) {
			$this->info('Run these commands in order to complete the build process.');

			$headers = ['ID', 'CLI Command', 'What it does'];

			$rows = [];
			for ($i = 0, $iMax = count($this->progressLog['instructions']); $i < $iMax; $i++) {
				$rows[] = array_merge([$i + 1], $this->progressLog['instructions'][$i]);
			}

			$this->table($headers, $rows);
			$this->info('');
		}

		foreach ($this->progressLog['info'] as $message) {
			$this->info($message);
		}

		if (is_countable($this->progressLog['errors']) && count($this->progressLog['errors'])) {
			$this->error('THESE ERRORS WERE DETECTED:');
			foreach ($this->progressLog['errors'] as $message) {
				$this->error($message);
			}
		}
	}

	/**
	 *
	 * Update Read Me File
	 *
	 * @return bool|null
	 */
	protected function updateReadMeFile()
	{
		if (is_countable($this->progressLog['instructions']) && count($this->progressLog['instructions'])) {
			$title = '## Local Development Setup Instructions';
			$filePath = base_path('readme.md');

			try {
				if (FileEditor::isTextInFile($filePath, $title)) {
					return false;
				}
			} catch (FileNotFoundException $ex) {
				$this->error('README.md file not found at ' . $filePath);
				return false;
			}

			$lines = [];
			$lines[] = "\r";
			$lines[] = $title;
			$lines[] = " ";

			for ($i = 0, $iMax = count($this->progressLog['instructions']); $i < $iMax; $i++) {
				$instruction = $this->progressLog['instructions'][$i];
				if (is_countable($instruction) && count($instruction) === 2) {
					$lines[] = "- `{$instruction[0]}` - {$instruction[1]}";
				} else {
					$lines[] = "- " . $instruction[0];
				}
			}

			$content = implode("\r\n", $lines);

			File::append($filePath, $content);

			$this->info("README.md file updated with build instructions.");
		}
	}

	protected function replaceIn($path, $search, $replace)
	{
		if (! file_exists($path)) {
			$this->progressLog['errors'][] = $path . ' not found to update.';
			return false;
		}

		FileEditor::findAndReplace($path, $search, $replace);
	}
}
