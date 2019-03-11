<?php
require './../../vendor/autoload.php';

use Eagle\Route\Router;
use Symfony\Component\HttpFoundation\Request;

$router = new Router();
$router->get('/articles', function () {
    return 'This is articles list';
});

$router->get('/articles/{id:\d+}', function ($id) {
    return 'Article id: ' . $id;
});

$router->get('/articles/{id:\d+}[/{title}]', function ($id, $title) {
    return 'Article id: ' . $id . ', title: ' . $title;
});

$request = new Request();
$routeHandler = $router->getRouteHandler();
$response = $routeHandler->handle($request);
echo $response;
