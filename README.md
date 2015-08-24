Silex Manager
=============

Silex Manager is a easy way to create crap CRUD based application.
We know that CRUD based apps is so bad, but It's needed some times.

## Author

- [Jefersson Nathan](https://github.com/malukenho)

## Installing

```
$ composer require malukenho/silex-manager
```

## Requirements

- twig/twig
- reduce/db
- symfony/twig-bridge
- session
- symfony/form
- symfony/security-csrf
- symfony/translation

## Usage

First of all, you need to register the `ManagerControllerProvider` to your `Silex\Application`.
For now you should pass a `PDO` instance to the our provider.

```php
$silex->mount('/manager', new Manager\Controller\ManagerControllerProvider($pdo));
```

## Routes

| Router                        |  Bind   |          |
|-------------------------------|---------|----------|
| /{dbTable}/page/{pageNumber}  |         |          |

