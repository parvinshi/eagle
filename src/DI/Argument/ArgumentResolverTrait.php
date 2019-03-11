<?php
declare(strict_types=1);

namespace Eagle\DI\Argument;


use Psr\Container\ContainerInterface;
use Eagle\DI\Exception\NotFoundException;
use ReflectionFunctionAbstract;
use ReflectionParameter;

trait ArgumentResolverTrait
{
    /**
     * @param array $arguments
     * @return array
     */
    public function resolveArguments(array $arguments) : array
    {
        $container = $this->getContainer();
        $arguments = array_map(function ($arg) use ($container) {
            if (is_string($arg)) {
                if (isset($container) && $container->has($arg)) {
                    $entry = $container->get($arg);
                    $arg = $entry ?? $arg;
                }
            }

            return $arg;
        }, $arguments);

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []) : array
    {
        $arguments = array_map(function (ReflectionParameter $param) use ($method, $args) {
            $name  = $param->getName();
            $class = $param->getClass();

            if (array_key_exists($name, $args)) {
                return $args[$name];
            }

            if (!is_null($class)) {
                return $class->getName();
            }

            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }

            throw new NotFoundException(sprintf(
                'Unable to resolve a value for parameter (%s) in the function/method (%s)',
                $name,
                $method->getName()
            ));
        }, $method->getParameters());

        return $this->resolveArguments($arguments);
    }

    abstract public function getContainer() : ContainerInterface;
}