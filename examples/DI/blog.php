<?php

declare(strict_types=1);

namespace Blog;

require './../../vendor/autoload.php';
use Eagle\DI\Container;

class Controller
{
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}

class Model
{
    public $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

$container = new Container;

$container->set(Controller::class)->addArguments(Model::class);
$container->set(Model::class)->addArguments(\PDO::class);

$container->set(\PDO::class)
    ->addArguments(['mysql:dbname=test;host=localhost', 'root', '111111']);

$controller = $container->get(Controller::class);

var_dump($controller instanceof Controller); // true
var_dump($controller->model instanceof Model); // true
var_dump($controller->model->pdo instanceof \PDO); // true