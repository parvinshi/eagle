<?php
declare(strict_types=1);

namespace AutoWiring;

require './../../vendor/autoload.php';

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


