<?php
declare(strict_types=1);

namespace Examples;

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

    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

$container = new Container;

$container->set(Controller::class, function () {

    $pdo   = new \PDO('mysql:dbname=test;host=localhost', 'root', '111111', [/* options */]);
    $model = new Model;

    $model->setPdo($pdo);

    return new Controller($model);
});

$controller = $container->get(Controller::class);

var_dump($controller instanceof Controller); // true
var_dump($controller->model instanceof Model); // true
var_dump($controller->model->pdo instanceof \PDO); // true



