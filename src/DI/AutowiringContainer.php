<?php
declare(strict_types=1);

namespace Eagle\DI;


use Eagle\DI\Argument\ArgumentResolverTrait;
use Psr\Container\ContainerInterface;
use Eagle\DI\Exception\NotFoundException;
use ReflectionClass;

class AutoWiringContainer implements ContainerInterface, ContainerSourceInterface
{
    use ArgumentResolverTrait;
    use ContainerTrait;

    private $useCache = false;

    private $resolved = [];

    /**
     * {@inheritdoc}
     */
    public function get($id, array $args = [])
    {
        // TODO: Implement get() method.
        if ($this->useCache === true && array_key_exists($id, $this->resolved))
        {
            return $this->resolved[$id];
        }

        if (!$this->has($id)) {
            throw new NotFoundException(
                sprintf('Name (%s) is not an existing class and therefore cannot be resolved', $id)
            );
        }

        $reflector = new ReflectionClass($id);
        $constructor = $reflector->getConstructor();

        $resolved = is_null($constructor) ? new $id : $reflector->newInstanceArgs($this->reflectArguments($constructor, $args));

        if ($this->useCache === true) {
            $this->resolved[$id] = $resolved;
        }

        return $resolved;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id) : bool
    {
        // TODO: Implement has() method.
        return class_exists($id);
    }

    /**
     * 是否使用缓存
     * @param bool $used
     * @return AutowiringContainer
     */
    public function isUseCache(bool $used = true) : self
    {
        $this->useCache = $used;

        return $this;
    }

}