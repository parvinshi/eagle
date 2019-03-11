# eagle-framework

```Eagle框架实现了依赖注入容器（Dependency Injection Container）、路由（route）等组件,其它如ORM、cache、filesystem、session、validation等组件可以使用composer来由用户自由扩展。```

# composer安装
```composer require parvin/eagle-framework```

# php版本要求
```PHP7以上```

# Route 
route可以使用symfony的http foundation组件来处理HTPP消息请求（http messages）。
```
<?php
require 'vendor/autoload.php';

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

/*匹配处理路由组*/
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
```

#Container
Dependency injection Container基于PSR-11规范实现，包括3种注入实现方式：构造方法注入（Constructor Injection）、setter方法或属性注入（Setter Injection）、回调匿名函数注入。

[构造方法注入（Constructor Injection)]
```
<?php 
declare(strict_types=1);
namespace Examples;
use Eagle\DI\Container;

/**
* 构造方法注入（Constructor Injection)
*/
class Foo
{
    /**
     * @var \Examples\Bar
     */
    public $bar;

    /**
     * Foo constructor.
     * @param \Examples\Bar $bar
     */
    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}

/*class Bar {

}*/

class Bar {
    public $baz;

    public function __construct(Baz $baz)
    {
        $this->baz = $baz;
    }
}

class Baz {

}

$container = new Container;
$container->set(Foo::class)->addArguments(Bar::class);
$container->set(Bar::class)->addArguments(Baz::class);

$foo = $container->get(Foo::class);

var_dump($foo, $foo->bar);
var_dump($foo instanceof Foo);  // true
var_dump($foo->bar instanceof Bar); // true
var_dump($foo->bar->baz instanceof Baz); // true
```

setter方法注入
```
<?php
declare(strict_types=1);

namespace Examples;

require 'vendor/autoload.php';

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

$container->set(Controller::class)->addArguments(Model::class);
$container->set(Model::class)->addInvokeMethod('setPdo', [\PDO::class]);

$container->set(\PDO::class)
    ->addArguments(['mysql:dbname=test;host=localhost', 'root', '111111']);

$controller = $container->get(Controller::class);

var_dump($controller instanceof Controller); // true
var_dump($controller->model instanceof Model); // true
var_dump($controller->model->pdo instanceof \PDO); // true
```
匿名函数注入
```
<?php
declare(strict_types=1);

namespace Examples;

require 'vendor/autoload.php';

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
```

#自动布线（auto wiring）
```
<?php
declare(strict_types=1);

namespace AutoWiring;

require 'vendor/autoload.php';

use Eagle\DI\ContainerBuilder;

class Foo
{
    /**
     * @var \AutoWiring\Bar
     */
    public $bar;

    /**
     * @var \AutoWiring\Baz
     */
    public $baz;

    /**
     * Construct.
     *
     * @param \AutoWiring\Bar $bar
     * @param \AutoWiring\Baz $baz
     */
    public function __construct(Bar $bar, Baz $baz)
    {
        $this->bar = $bar;
        $this->baz = $baz;
    }
}

class Bar
{
    /**
     * @var \AutoWiring\Bam
     */
    public $bam;

    /**
     * Construct.
     *
     * @param \AutoWiring\Bam $bam
     */
    public function __construct(Bam $bam)
    {
        $this->bam = $bam;
    }
}

class Baz
{
    // ..
}

class Bam
{
    // ..
}

$container = new ContainerBuilder;
$container = $container->build();

$foo = $container->get(Foo::class);

var_dump($foo instanceof Foo);           // true
var_dump($foo->bar instanceof Bar);      // true
var_dump($foo->baz instanceof Baz);      // true
var_dump($foo->bar->bam instanceof Bam); // true
```
