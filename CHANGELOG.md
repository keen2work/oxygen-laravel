# Change Log

## Version Compatibility

| Laravel Version | Oxygen Version | Branch       |
|----------------:|----------------|--------------|
|      Laravel 10 | 7.0.0          | 7.x          |
|     Laravel 9.2 | 6.1.x          | 6.x          |
|       Laravel 9 | 6.0            | 6.x          |
|       Laravel 8 | 5.x            | 5.x          |
|       Laravel 7 | 4.x            | version/v4.x |
|              v6 | 3.x            |
|             5.8 | 2.3.x          |
|             5.7 | 2.2.20         |
|             5.6 | 1.1.6          |
|             5.4 | 1.0.8          |
|             5.3 | 0.3.2          |
|             5.2 | 0.1.4          |

## v7.0.0
- Support for Laravel 10
- Minimum PHP version is now 8.1
- DB: `password_resets` tabled renamed to `password_reset_tokens` by Laravel.

## v6.1
- Webpack replaced with Vite to support Laravel 9.2. If you want to use Webpack, follow [Laravel's Official guide](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-vite-to-laravel-mix).
- `typeahead.js`, `browsersync` dependencies removed.
- Default namespace removed from RouteServiceProvider.
- Default web routes moved to `routes/oxygen-web.php`
- Use of `data-toggle` is replaced with `data-bs-toggle` to support Bootstrap 5.

## v6.0
- Support Laravel 9
- Dropped Php 7 Support
- Upgraded bouncer to v1.0.0-rc.12 
- Replaced `cviebrock/eloquent-sluggable` with  `spatie/laravel-sluggable`
- Upgrade to Bootstrap 5

## v5.1
- Support for PHP 8
- Upgraded bouncer from v1.0.0-rc.9 to v1.0.0-rc.10

## v5.0
- Support for Laravel 8
- Complete rewrite of the architecture. This will not work with Laravel 7 or past versions.
- PHP v7.3+ required by Laravel 8
- `$repository->search()` is renamed to `$respository->searchPaginate()`
- `location` database field now only contains `latitude` and `longitude`. What used to be `location` field is now called `place`.

## Version 4
- **4.2** Added API Test Generation (via emedia/api)
- **4.1.0** Added Dusk Tests
- **4.0.2** Files are now attachable to other objects
- Upgraded to support Laravel 7
- Auto-sync feature of nested relationships removed
- Bower usage removed. Now all client side libraries should be compiled through Laravel Mix.
- `emedia/helpers` package renamed to `emedia/laravel-helpers`
- Cleaned up files in `resources`

## Version 3
- Upgraded to support Laravel 6
- Added Local Development Notes in `DEVELOPMENT.md`
- Added script to sync source files from main Laravel repo

## Version 2.2.5
- Added Input stream parser for PUT requests
- Changed API profile `POST` to `PUT`

## Version 2.2.0
- Added API builder to dashboard
- Added files section to dashboard
- Added default API login controllers
- Added device management section to dashboard
- Changed default readme.md file

## Version 2.1.0

- Auto-generate UUID for Users
- Added Users to Dashboard
- Added User disabling, safe deletes with PII removal
- Added Scaffolding for default views with `scaffold:views` command
- Added standard date/time formats
- Separated Users migration file

## Version 2.0

- Laravel 5.7 Support
- Admin panel Upgraded to Bootstrap 4
- Simplified setup command with `--confirm` flag
- Authentication routes changed
- All dependent packages refactored
- Added feature flags
- Added `App Settings` section by default
- User profile options moved into main dashboard area
- Package Auto-discovery added
- `Readme.md` auto-updated after the setup process with build instructions
- Removed multi-tenant option temporarily
- Added Recaptcha to contact forms, fixed contact-us mail

## Version 1.1

- Laravel 5.6
