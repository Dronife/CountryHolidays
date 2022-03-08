# Docker Symfony (PHP-FPM - NGINX - MySQL)

## Installation

Just copy files from this repository to your project root directory.
- merge .gitignore with your project .gitignore

## Usage

1. Run `cp docker-compose.override.linux.dist.yaml docker-compose.override.yaml`
2. Update your system host(`/etc/hosts`)
    ```bash
    $ sudo echo "172.11.11.10 symfony.test" >> /etc/hosts
    ```
     **Running on mac:**
    ```bash
    $ sudo echo "127.0.0.1 symfony.test" >> /etc/hosts
    ```
3. Build/run containers
    ```bash
    $ ./start-dev.sh
    ```
4. Run symfony app
    1. Update .env DATABASE_URL value(check `./docker-compose.override.yaml` to find a database host, database name,
       user and password values)
   2. Run `./backend.sh` to connect to a php docker container
       1. set rwx permissions

       - `HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d \ -f1)`
       - `setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var/log var/cache`
       - `setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var/log var/cache`

       2. run `composer install`
       3. run `php bin/console doctrine:migrations:migrate`
5.  - Website [symfony.test](http://symfony.test)
        - Mac: [symfony.test:8080](http://symfony.test:8080)
    - Phpmyadmin [symfony.test:8888](http://symfony.test:8888)
6. Api documentation in route `/api/doc`
7. Stop containers
    ```bash
    $ ./stop-dev.sh
    ```

* Logs (files location): logs/nginx and logs/symfony

## Xdebug configuration(for PhpStorm)

1. Open server configuration in PHPSTORM: File > Settings > Languages & Frameworks > PHP > Servers
2. In the server configuration page press '+' to add a new server configuration
3. Please enter these setings:
    * 'Name' - you can choose whatever you want
    * 'Host' - Please enter the same server_name as you are set on your nginx configuration(docker/nginx/symfony.conf),
      by default - 'symfony.test'
    * 'PORT' - 80 and 'Debugger' - Xdebug
    * Check the checkbox on 'Use path mappings'
    * Set two path in 'Absolute path on the server':
        * First path '/var/www/symfony'(this directory you set on docker/php-fpm/Dockerfile 'WORKDIR') must specify your
          local project directory;
        * Second path 'var/www/symfony/public' must specify your_local_project_directory/public directory
4. Press 'apply' and 'save'
5. Turn on 'Start listening for PHP Debug Connections' on your PhpStorm.

That's it! You are ready for debugging, enjoy!

## Useful commands

```bash
# View specific container logs
$ docker ps -a
$ docker logs CONTAINER_ID

# bash commands
$ docker-compose exec php bash

# MySQL commands
$ docker-compose exec db mysql -uroot -p"root"

# Check CPU consumption
$ docker stats $(docker inspect -f "{{ .Name }}" $(docker ps -q))

# Delete all containers
$ docker rm $(docker ps -aq)

# Delete all images
$ docker rmi $(docker images -q)
```
