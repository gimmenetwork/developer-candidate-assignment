# Installation

The app is developed in the Docker environment. To run the Docker jump to the [Getting Started](#getting-started) section.

First, you should run the migrations, and then if you want, you can load the initial data.
```
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixture:load -n
```

The default user credential is
```
username: admin@example.com
password: password
```

# API
The API endpoint for the books listing is `/api/books`

There are some query parameters:
```
page   - The default page value
limit  - The limit for the pagination
author - You can search the books with this parameter based on author-name
genre  - You can search the books with this parameter based on genre-name
```

Example requests:
```
curl https://localhost/api/books
curl https://localhost/api/books?page=1&limit=2
curl https://localhost/api/books?limit=5&author=Dan
curl https://localhost/api/books?limit=5&author=Dan&genre=Thriller
```

Or if you use PhpStorm you can check the `http_client_requests_for_api/requests.http` file

# Test
```
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load

php vendor/bin/phpunit
```
---
# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker-compose build --pull --no-cache` to build fresh images
3. Run `docker-compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)

## Features

* Production, development and CI ready
* Automatic HTTPS (in dev and in prod!)
* HTTP/2, HTTP/3 and [Preload](https://symfony.com/doc/current/web_link.html) support
* Built-in [Mercure](https://symfony.com/doc/current/mercure.html) hub
* [Vulcain](https://vulcain.rocks) support
* Just 2 services (PHP FPM and Caddy server)
* Super-readable configuration

**Enjoy!**

## Docs

1. [Build options](docs/build.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Installing Xdebug](docs/xdebug.md)
6. [Troubleshooting](docs/troubleshooting.md)

## Credits

Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).

---

# 2021 Developer Assignment

Develop a library application with following features:

1. Add/Edit/Remove books from stock, each book must have Name, Author, Genre
2. Add/Edit Readers; each reader must have at least a name
3. Lease a Book to a Reader, return date is mandatory. Reader can not have more than three books.
4. Expose restful API that talks JSON for listing books and their availablility, searching by author and by genre

Application must be developed using PHP7.4+ and Symfony 5.x. Solutions employing other frameworks will not be accepted.

Resulting code must have meaningful tests.

You can use whatever tools you find necessary and helpful, except jQuery and API Platform.

Bonus points for:

1. Proper security implementation
2. Meaningful documentation
3. Least amount of dependencies
4. Nice UI and UX

You will have 72h to complete the task. You will need to fork this repository and create a PR on completion, setting @kstupak as a reviewer.
