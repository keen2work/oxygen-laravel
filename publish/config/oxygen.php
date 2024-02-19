<?php

return [

	// static API KEY for the application
	'api_key'	=> env('API_KEY', false),

	// disable dashboard login in dev for easier testing
	'disable_dashboard_security' => env('DISABLE_DASHBOARD_SECURITY', false),

	// page display settings
	'dashboard' => [
		'default_page_title' => 'My Account',
		'per_page' => 50,
	],

	// Standard Date formats
	'date_format' => 'd/M/Y',

	'date_time_format' => 'd/M/Y h:i A',

	'time_format' => 'h:i A',

	// file/size limits
	'maxFileSizeMb' => env('MAX_UPLOAD_SIZE_MB', 2),

	// Auth Model assignment
	'abilityModel'		=> \App\Entities\Auth\Ability::class,
	'abilityRepository' => \App\Entities\Auth\AbilityRepository::class,
	'roleModel'			=> \App\Entities\Auth\Role::class,
	'roleRepository'	=> \App\Entities\Auth\RolesRepository::class,

	'tenantModel'		=> \EMedia\Oxygen\Entities\Auth\MultiTenant\Tenant::class,
	'tenantRepository'  => \EMedia\Oxygen\Entities\Auth\MultiTenant\TenantRepository::class,
	'multiTenantActive' => false,

	'invitationRepository'	=> \EMedia\Oxygen\Entities\Invitations\InvitationRepository::class,

	'api' => [
		// include the model definitions on the API output. (You may disable this for security)
		'includeModelDefinitions' => true,

		// hide these models from API definition for security
		'hiddenModelDefinitionClasses' => [
			'Role',
			'Ability',
			'AbilityCategory',
		],

		// add additional directories to look for models,
		'modelDirectories' => [
			// app_path('Models'),
		],

		// API Test User
		// this user will be used as the test user for API Tests
		// the user's access_token will be used as the `x-access-token` to generate test results
		'testUserId' => 4,
	],
];
