<?php
declare(strict_types=1);

namespace Eagle\DI\Definition;

use Eagle\DI\ContainerTrait;
use Eagle\DI\Exception\NotFoundException;

class DefinitionCollection implements DefinitionCollectionInterface, \ArrayAccess
{
    use ContainerTrait;

    private $services = [];

    protected $definitions = [];

    public function __construct(array $definitions = [])
    {
        $this->definitions = array_filter($definitions, function($definition) {
            return $definition instanceof DefinitionInterface;
        });
    }

    /**
     * 为某个类添加对象依赖的定义对象
     *
     * @param string $id
     * @param $definition
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function set(string $id, $definition, bool $shared = false): DefinitionInterface
    {
        if ($this->has($id)) {
            return $this->services[$id];
        }

        // TODO: Implement set() method.
        if (!$definition instanceof DefinitionInterface) {
            $definition = new Definition($id, $definition);
        }

        $this->services[$id] = $definition;
        $this->definitions[] = $definition->setName($id)->setShared($shared);

        if (!isset($definition->container)) {
            $definition->setContainer($this->getContainer());
        }

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        // TODO: Implement has() method.
        foreach ($this->getIterator() as $definition) {
            if ($id === $definition->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $id, bool $isNew = false)
    {
        // TODO: Implement resolve() method.
        return $this->getDefinition($id)->resolve($isNew);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(string $id): DefinitionInterface
    {
        // TODO: Implement getDefinition() method.
        foreach ($this->getIterator() as $definition) {
            if ($id === $definition->getName()) {
                return $definition->setContainer($this->getContainer());
            }
        }

        throw new NotFoundException(sprintf('Name (%s) is not being handled as a definition.', $id));
    }

    public function getIterator() : \Generator
    {
        // TODO: Implement getIterator() method.
        $count = count($this->definitions);

        for ($i = 0; $i < $count; $i++) {
            yield $this->definitions[$i];
        }
    }

    public function offsetExists($id)
    {
        // TODO: Implement offsetExists() method.
        return isset($this->services[$id]);
    }

    public function offsetGet($id)
    {
        // TODO: Implement offsetGet() method.
        return isset($this->services[$id]) ? $this->services[$id] : null;
    }

    public function offsetSet($id, $value)
    {
        // TODO: Implement offsetSet() method.
        $this->services[$id] = $value;
    }

    public function offsetUnset($id)
    {
        // TODO: Implement offsetUnset() method.
        unset($this->services[$id]);
    }

}