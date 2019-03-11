<?php
declare(strict_types=1);

namespace Examples;
require './../../vendor/autoload.php';

use Eagle\DI\Container;

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
/*
class Examples\Foo#10 (1) {
  public $bar =>
  class Examples\Bar#11 (1) {
    public $baz =>
    class Examples\Baz#8 (0) {
    }
  }
}

class Examples\Bar#11 (1) {
  public $baz =>
  class Examples\Baz#8 (0) {
  }
}
*/
var_dump($foo instanceof Foo);  // true
var_dump($foo->bar instanceof Bar); // true
var_dump($foo->bar->baz instanceof Baz); // true

