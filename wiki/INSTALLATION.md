# Oxygen Installation Instructions

## Installation

This package is intended to be installed on a **new project**. You'll be able to install it on an existing project, but might need to change some configuration settings.

### Install Method 1: Use the Installer (Recommended)

The easiest way to do it is by using the [Oxygen Installer](https://bitbucket.org/elegantmedia/oxygen-installer). With the installer, you can create a project with one line.

```
oxygen new myproject --name 'Oxygen' --email apps@elegantmedia.com.au --devurl 'localhost.test'
```

### Install Method 2: Install Manually

If you don't want to use the installer, you can install it manually.

#### 1. Create a New Laravel Project
```
// Create the project
composer create-project --prefer-dist laravel/laravel="10.*" [project-name]

// Go to the directory
cd [project-name]
```

#### 2. Install Oxygen

2.1. Update `composer.json`

This package and some dependent packages are available in private repositories. Change the `repositories` section to add the new repository, or create a new section in the file.

```
    "repositories": [
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/oxygen-laravel.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/devices-laravel.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/laravel-app-settings.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/laravel-api-helpers.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/laravel-media-manager.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/laravel-test-kit.git"
        },
         {
             "type":"vcs",
             "url":"git@bitbucket.org:elegantmedia/lotus.git"
         },
         {
             "type": "vcs",
             "url": "git@bitbucket.org:elegantmedia/formation.git"
         },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/multitenant-laravel.git"
        }
    ],
```

2.2. Require the package into composer through the command line
```
composer require emedia/oxygen:"^7.0"
```

2.3. Edit `.env` file and update the database settings

#### 3. Run Setup

3.1. Initialize Git Repository 

Commit your current state to Git, because next step will change some of the default files.

```
git init
git add -A && git commit -m "Initial commit."
```

3.2. Run Oxygen Setup

Run the following command. This will do the default installation, if any questions are asked, you can just press ENTER to confirm the default choice, or change it.

```
php artisan oxygen:dashboard:install --name Workbench --dev_url workbench.test --email apps@elegantmedia.com.au --dbname workbench --dbpass root --mailhost "0.0.0.0" --mailport 1025
```

Then follow instructions on screen.
