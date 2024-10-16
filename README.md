## Table of contents

* [General info](#general-info)
* [Current works](#current-works)
* [Tools](#tools)
* [Current features](#current-features)
* [Setup](#setup)
* [Test](#test)
* [Screenshot](#screenshots)

## General info

Games "Checkers" and "Chess"

## Current works
- Chess

## Tools

- Docker v27.1.2
- Symfony v7.1.1
- Twig v.3.10.3
- PHP v8.2
- Mysql v8.0.33
- Bootstrap v5.2.2
- Mercure-bundle v0.3.9
- Doctrine-messenger v7.1
- Redis v.7.4.0

## Current features

- Ability to play for two players
- Ability to play multiple games at the same time
- Ability to play with computer
- Logger
- Authorization through social networks
- Server-Sent-Events
- Async/Queued Messages

## Setup

Copy the .env.dist file and edit the entries to your needs:

```
cp .env.example .env
```

Start docker-compose to start your environment:

```
docker-compose up
```

Install Packages

```
docker exec php composer install
```

Migrate migrations

```
docker exec php ./vendor/bin/doctrine-migrations migrations:migrate
```

## Test

Copy the phpunit.xml file and edit the entries to your needs:

```
cp phpunit.xml.dist phpunit.xml
```

Create schemas for testing

```
docker exec php bin/console --env=test doctrine:schema:create
```

Run tests

```
docker exec php bin/phpunit
```

## Screenshots

![Login](public/pictures/login.png)
![Game_list](public/pictures/game-list.png)
![Game](public/pictures/game.png)
![Chess](public/pictures/chess.png)