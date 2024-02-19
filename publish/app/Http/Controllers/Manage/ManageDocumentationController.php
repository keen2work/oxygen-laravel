<?php


namespace App\Http\Controllers\Manage;


class ManageDocumentationController
{

	public function index()
	{
		$apiKeys = array_filter(explode(',', env('API_KEY', '')));

		$filteredPaths = [];

		$files = [
			[
				'name' => 'API Documentation',
				'file_path' => '/docs/api/index.html',
			],
			[
				'name' => 'Swagger UI Loader',
				'file_path' => '/docs/swagger.html',
			],
			[
				'name' => 'Swagger API Specification (JSON)',
				'file_path' => '/docs/swagger.json',
			],
			[
				'name' => 'Swagger API Specification (YAML)',
				'file_path' => '/docs/swagger.yml',
			],
			[
				'name' => 'Postman Collection File (JSON)',
				'file_path' => '/docs/postman_collection.json',
				'description' => 'Import this file directly to Postman for testing',
			],
			[
				'name' => 'Postman Collection File (YAML)',
				'file_path' => '/docs/postman_collection.yml',
				'description' => 'Import this file directly to Postman for testing',
			],
			[
				'name' => 'Postman Environment File',
				'file_path' => '/docs/postman_environment.json',
				'description' => 'Postman environment variables for the collection',
			],
			[
				'name' => 'Postman (LOCAL) Environment File',
				'file_path' => '/docs/postman_environment_local.json',
				'description' => 'Postman LOCAL environment variables for the collection',
			],
			[
				'name' => 'Postman (SANDBOX) Environment File',
				'file_path' => '/docs/postman_environment_sandbox.json',
				'description' => 'Postman SANDBOX environment variables for the collection',
			],
		];

		foreach ($files as $file) {
			if (file_exists(public_path($file['file_path']))) {
				$filteredPaths[] = $file;
			}
		}

		return view('oxygen::manage.documentation.index', [
			'pageTitle' => 'API Documentation',
			'paths' => $filteredPaths,
			'apiKeys' => $apiKeys,
		]);
	}

}