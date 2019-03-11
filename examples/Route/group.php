<?php
require './../../vendor/autoload.php';

use Eagle\Route\Router;
use Symfony\Component\HttpFoundation\Request;

$router = new Router();
$router->group('/articles', function () use ($router) {
    $router->get('/list', function() {
        return 'This is articles list';
    });

    $router->get('/detail', function ($id, $title) {
        return 'Article detail id: ' . $id . ', title: ' . $title;
    });
});

$request = new Request();
$routeHandler = $router->getRouteHandler();
$response = $routeHandler->handle($request);
echo $response;