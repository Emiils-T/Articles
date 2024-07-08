<?php

require_once './vendor/autoload.php';

use App\Services\CreateArticleService;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Database\ArticleDatabase;

/*
$article = new Article('test1','string content',Carbon::now());
*/
$articleDatabase = new ArticleDatabase('Storage/Database.sqlite');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$loader = new FilesystemLoader(__DIR__ . '/Templates');
$twig = new Environment($loader, [
    'cache' => false,
]);

switch ($_ENV['APP_LOGGER'])
{
    case 'monolog':
        $logger = (new Logger('app'))->pushHandler(
            new StreamHandler('Storage/logs/app.log', Logger::DEBUG)
        );
        break;
    default:
        $logger = new App\EmptyLogger();
        break;
}
$container = new DI\Container();

$container->set(
    LoggerInterface::class,
    $logger
);
$container->set(
    ArticleDatabase::class,
    new ArticleDatabase('Storage/Database.sqlite')
);
$container->set(
    CreateArticleService::class,
    new CreateArticleService($articleDatabase, $logger)
);


$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes= include("routes.php");
    foreach ($routes as $route)
    {
        [$method,$url,$controller] = $route;
        $r->addRoute($method, $url, $controller);
    }
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = $handler;
        /*$controllerInstance = new $controller($articleDatabase);*/



        $response = ($container->get($controller))->{$method}(...array_values($vars));

        /*var_dump($response->getData());*/
        if ($response instanceof App\Response)
        {
            echo $twig->render($response->getTemplate(), $response->getData());
        }

        if ($response instanceof App\RedirectResponse)
        {
            header('Location: '. $response->getLocation());
        }



        break;
}