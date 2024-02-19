# Contributing

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - Check the code style with `composer check-style` and fix it with `composer fix-style`.
- **Create feature branches** - Don't commit to master branch.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Local Project Setup

- Step 1 - Clone the repo to your local machine and move to that folder.
- Step 2 - Create a new branch on the cloned project. For example `feature/202003-add-settings`
- Step 3 - The easiest way for development is to setup a second Laravel project which will act as the parent project. For this example, we'll create a project called `Workbench` and it will have the local development URL `http://workbench.test`

```
composer create-project --prefer-dist laravel/laravel="10.*" Workbench
cd Workbench
```

If you'd like to fetch the project from another branch, use the syntax below, where `dev-master` is the branch name.
```
composer create-project --prefer-dist laravel/laravel Workbench dev-master
```

- Step 4 - Then open `composer.json` on the test project, which will be located at `Workbench/composer.json`, and add the following config. This will [create a symlink](https://getcomposer.org/doc/05-repositories.md#path) to your cloned project. The `url` will be the path created on Step 1.

```
"repositories": [
    {
        "type": "path",
        "url": "../your-local-oxygen-cloned-path"
    }
]
```

In addition, go to [./INSTALLATION.md] and follow Step #2.1.

- Step 5 - Now add this project, but use the branch created at Step 2.

``` shell
composer require emedia/oxygen:"dev-feature/202003-add-settings"
```

OR Alternatively, you can add it to `composer.json`, and run `composer update`.
``` json 
	"emedia/oxygen": "dev-feature/202003-add-settings"
```

- Step 7 - Create a database called `workbench`

- Step 8 - Update the settings on the `.env` file on the test project.

- Step 9 - At this point commit your changes to the TEST PROJECT, so you can rollback to this point easily. Note that you're NOT COMMITING changes to oxygen project. The changes are commited to the TEST project, in this example, `Workbench` folder.

```shell
# Commit your changes to the TEST project
git init
git add . && git commit -m "Initial commit"
```

```shell
# Reset the TEST project to the previous commit and remove unstaged changes
# Do this if you want to rollback to the last commit and remove unstaged changes
git reset --hard HEAD~1 && git clean -fd
```

## Test Installation and Rollback

Now you can run the test installer.

```
php artisan oxygen:dashboard:install --name Workbench --dev_url workbench.test --email apps@elegantmedia.com.au --dbname workbench --dbpass root --mailer "log" --mailhost "0.0.0.0" --mailport 1025
```

Migrate and seed

```
php artisan db:refresh
```

Do the NPM Installs
```
npm install --legacy-peer-deps
```

Run the project

```
npm run dev
// OR, build it with
npm run build
```

Now you can update files on either projects. If there are any errors, rollback to last commit.


## Development Tools

Periodically, you'll have to sync files from Laravel's main project back so the local files are updated. Use the following command to do that.

```
php ./setup/SyncFromSource.php
```

Run the PHPUnit tests

```
vendor/bin/phpunit
```

#### Run Dusk Tests

```
-- Install dusk
composer require --dev laravel/dusk
php artisan dusk:install

-- (optional) Set driver to a specific version
php artisan dusk:chrome-driver 107

-- Open a new tab and run the server
php artisan serve

-- Run dusk tests
php artisan dusk --stop-on-error --stop-on-failure
```


## Production Tools

Always ensure the tests pass before you send a pull-request.

Before releasing a version, the assets must be pre-compiled on the `publish` directory.

```
cd publish
npm run build
# This will create a `node_modules` folder in the `publish` directory. But we don't want that.
# So remove it.
rm -rf node_modules
```

## Troubleshooting

Here are some common errors, and how to fix them.

#### Error: Dusk Tests Fail with `Curl error thrown for http POST to /session with param:`

This is usually caused by the Chrome driver not being compatible with the Chrome browser. 
To fix this, run the following command to update the driver.

This command will download the appropriate driver version for your Chrome browser.

```
php artisan dusk:chrome-driver
```

#### Turn off the headless mode to see Dusk Preview

If you want to see the browser while running the dusk tests, you can turn off the headless mode by commenting the following line in `tests/DuskTestCase.php`

```
// '--headless=new',
```






