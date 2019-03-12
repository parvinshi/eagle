<?php
declare(strict_types=1);

namespace Eagle\DI\Definition;


use Eagle\DI\Argument\ArgumentResolverTrait;
use Eagle\DI\ContainerTrait;

class Definition implements DefinitionInterface
{
    use ContainerTrait;
    use ArgumentResolverTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $entry;

    /**
     * @var bool
     */
    protected $shared = false;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $methods = [];

    protected $resolved;

    public function __construct(string $id, $entry)
    {
        $entry = $entry ?? $id;

        $this->name = $id;
        $this->entry = $entry;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $id) : DefinitionInterface
    {
        $this->name = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntry($definition) : DefinitionInterface
    {
        $this->entry = $definition;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntry() : string
    {
        return $this->entry;
    }

    /**
     * {@inheritdoc}
     */
    public function setShared(bool $shared = true) : DefinitionInterface
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isShared() : bool
    {
        return $this->shared;
    }

    /**
     * {@inheritdoc}
     */
    public function addArguments($args) : DefinitionInterface
    {
        $container = $this->getContainer();
        if (is_string($args)) {
            $this->arguments[] = $args;

            if (class_exists($args)) {
                /** 自动为依赖对象添加定义对象 */
                $container->set($args);
            }
        } elseif (is_array($args)) {
            $this->arguments = $this->arguments + array_values($args);

            array_walk($args, function ($arg) use($container) {
                if (class_exists($arg)) {
                    $container->set($arg);
                }
            });
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInvokeMethod(string $method, array $args = []) : DefinitionInterface
    {
        $this->methods[] = [
            'func' => $method,
            'args' => $args
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInvokeMethods(array $methods = []) : DefinitionInterface
    {
        array_walk($methods, function ($method, $args){
            $this->addInvokeMethod($method, $args);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(bool $isNew = false)
    {
        $entry = $this->entry;

        if ($this->isShared() && $this->resolved && !$isNew) {
            return $this->resolved;
        }

        if (is_callable($entry)) {
            $entry = $this->resolveCallable($entry);
        }

        if (is_string($entry) && class_exists($entry)) {
            $entry = $this->resolveClass($entry);
        }

        if (is_object($entry)) {
            $entry = $this->invokeMethods($entry);
        }

        $this->resolved = $entry;

        return $entry;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveCallable(callable $entry)
    {
        $resolved = $this->resolveArguments($this->arguments);

        return call_user_func_array($entry, $resolved);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveClass(string $entry)
    {
        $resolved = $this->resolveArguments($this->arguments);
        $reflection = new \ReflectionClass($entry);

        return $reflection->newInstanceArgs($resolved);
    }

    /**
     * @param $instance
     * @return object
     */
    protected function invokeMethods($instance)
    {
        array_walk($this->methods, function($method) use($instance) {
            $args = $this->resolveArguments($method['args']);
            call_user_func_array([$instance, $method['func']], $args);
        });

        return $instance;
    }
}