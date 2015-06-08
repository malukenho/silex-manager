<?php

require __DIR__ . '/vendor/autoload.php';

$pdo = new \PDO('mysql:host=localhost;dbname=manager', 'root', 'root');
$pdo->exec('SET NAMES "utf8"');

$silex = new Silex\Application();
$silex['debug'] = true;

$silex->register(new Silex\Provider\UrlGeneratorServiceProvider());
$silex->register(new Silex\Provider\SessionServiceProvider());
$silex->register(new Silex\Provider\FormServiceProvider());
$silex->register(new Silex\Provider\TranslationServiceProvider(), [
    'translator.domains' => [],
]);
$silex->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

// TODO: move config to readme.md
$silex['manager-config'] = [
    'manager' => [
        'users' => require __DIR__ . '/config/user.php',
    ],
    'view'    => [
        'index' => 'manager-index.twig',
        'new'   => 'manager-new.twig',
        'edit'  => 'manager-edit.twig',
    ],
];

$silex->mount('/manager', new Manager\Controller\ManagerControllerProvider(new \Manager\Db\Adapter\PdoAdapter($pdo)));

$silex->run();
