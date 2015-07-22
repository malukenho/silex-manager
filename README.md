Silex Manager
=============

Silex Manager is a easy way to create crap CRUD based application.
We know that CRUD based apps is so bad, but It's needed some times.

**Now you can create crap application in minutes**

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
$app->mount('/manager', new Manager\Controller\ManagerControllerProvider($pdo));
```

## Routes

| Router                        |       Bind     |
|-------------------------------|----------------|
| /{dbTable}/page/{pageNumber}  | manager-index  | 
| /{dbTable}/new                | manager-new    | 
| /{dbTable}/edit/{id}          | manager-edit   | 
| /{dbTable}/delete/{id}        | manager-delete | 

### Custom queries

Sometimes you will need make a custom query to show data on the list page.
This is possible by setting the key `query`.

```php
$app['manager-config'] = [
    'manager' => [
        'users' => [
            'index' => [
                'query' => 'SELECT * FROM users u INNER JOIN user_admin ua ON u.id = ua.id',
            ],
        ],
    ],
];
```

### Actions

### views

### Columns

### Before

### After

### Custom names

### Modifiers

