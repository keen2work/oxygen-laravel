# Test Deployment Pipeline on a Local Machine

You need these before starting.

1. Docker installed and running
2. A valid SSH key. Default is `~/.ssh/id_rsa`. This can be changed in `docker-compose.yml` and `setup/pipeline.sh` (This key is used to clone the project from Bitbucket. So it should have read access to all related private repos.)

Step 1: Setup all containers
```
docker-compose up
```

Step 2: SSH to PHP container (on a new tab/window)
```
docker exec -it appcontainer /bin/bash
```

Step 3: Run the shell script
The shell script will have similar commands to the Pipeline. Because now you're in the container, you'll have more control to see what happens, and can update the commands.
```
sh setup/pipeline.sh
```

Step 4: (Optional) See the application

Dusk Screenshots - you can copy the screenshots generated to a shared volume
```
// Run this from Docker container
rm /test_screenshots/*.png && cp /laravel_app/tests/Browser/screenshots/*.png /test_screenshots/
```

View the application
```
http://localhost:8095/
```

View the mail log
```
http://localhost:8025/
```

Connect to the Database
```
Host: 127.0.0.1
Port: 3305
User: root
Pass: root
```

Selenium Container
```
http://localhost:4444/
```

// Step 4: Update the files
If you make a change, ensure that you update BOTH `setup\pipeline.sh` and `bitbucket-pipelines.yml` with changes.


## Docker Commands

### 1. List all containers
```
docker ps -a
```

### 2. Stop all containers
```
docker stop $(docker ps -a -q)
```

### 3. Remove all containers
```
docker rm $(docker ps -a -q)
```

## Troubleshooting and Common Issues

#### 1. `Facebook\WebDriver\Exception\UnknownErrorException: unknown error: Chrome failed to start: crashed`

The chrome driver may be outdated. The solution is to update the `chromedriver` version. Check the latest version [here](https://hub.docker.com/r/selenium/standalone-chrome/tags). Then update the `chromedriver` version in `docker-compose.yml`, `pipeline.sh` and `bitbucket-pipelines.yml`.

#### 2. `Facebook\WebDriver\Exception\SessionNotCreatedException: Could not start a new session. Error while creating session with the driver service. Stopping driver service: Could not start a new session. Response code 500. Message: unknown error: Chrome failed to start: crashed`

This can be because of chromedriver version or if using on M1 Macs, used of ARM architecture. The solution is to use the an experimental [architecutre for the docker image](https://github.com/SeleniumHQ/docker-selenium#experimental-mult-arch-aarch64armhfamd64-images). Update the `docker-compose.yml` file to use a [compatible image](https://hub.docker.com/r/seleniarm/standalone-chromium/tags).

```
