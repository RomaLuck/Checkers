## Table of contents

* [General info](#general-info)
* [Tools](#tools)
* [Current features](#current-features)
    - [Current works](#current-works)
    - [ToDo](#todo)
* [Setup](#setup)
* [Test](#test)

## General info

Game "Checkers"

Currently, it is under development.

## Tools

- Symfony v7.1.1
- PHP v8.2
- Mysql v8.0.33
- Bootstrap v5.2.2
- Mercure

## Current features

- Ability to play for two players
- Ability to play multiple games at the same time
- Logger
- Authorization through social networks
- Server-Sent-Events

## Current works

- Game with computer

## ToDo

- Statistics

## Setup

Copy the .env.dist file and edit the entries to your needs:

```
cp .env.dist .env
```

Copy in app folder the .env file and edit the entries to your needs:

```
cp .env .env.local
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

```
docker exec php bin/phpunit
```
