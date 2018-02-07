<?php
// DIC configuration

/** @var Pimple\Container $container */

use Conduit\Middleware\OptionalAuth;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

$container = $app->getContainer();


// Error Handler
$container['errorHandler'] = function ($c) {
    return new \Conduit\Exceptions\ErrorHandler($c['settings']['displayErrorDetails']);
};

// App Service Providers
$container->register(new \Conduit\Services\Database\EloquentServiceProvider());
$container->register(new \Conduit\Services\Auth\AuthServiceProvider());

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('View', []);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

    $funn = new Twig_SimpleFunction('cur_uri', function () {
        return "hello world";
    });
    $view->getEnvironment()->addGlobal("current_path", $c["request"]->getUri()->getPath());
    $view->getEnvironment()->addFunction($funn);




    return $view;
};

// Jwt Middleware
$container['jwt'] = function ($c) {

    $jws_settings = $c->get('settings')['jwt'];

    return new \Slim\Middleware\JwtAuthentication($jws_settings);
};

$container['optionalAuth'] = function ($c) {
  return new OptionalAuth($c);
};


// Request Validator
$container['validator'] = function ($c) {
    \Respect\Validation\Validator::with('\\Conduit\\Validation\\Rules');

    return new \Conduit\Validation\Validator();
};

// Fractal
$container['fractal'] = function ($c) {
    $manager = new Manager();
    $manager->setSerializer(new ArraySerializer());

    return $manager;
};

// DB
$container['pdo'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(getenv('DB_CONNECTION').':host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};