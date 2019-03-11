<?php
declare(strict_types=1);

namespace Eagle\DI\ServiceProvider;


use Eagle\DI\ContainerTrait;

abstract class ServiceProviderAbstract implements ServiceProviderInterface
{
    use ContainerTrait;

    protected $provides = [];

    protected $identifier;

    /**
     * @param string $name
     * @return bool
     */
    public function provides(string $name): bool
    {
        // TODO: Implement provides() method.
        return isset($this->provides[$name]);
    }

    /**
     * @param string $id
     * @return ServiceProviderInterface
     */
    public function setIdentifier(string $id): ServiceProviderInterface
    {
        // TODO: Implement setIdentifier() method.
        $this->identifier = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        // TODO: Implement getIdentifier() method.
        return $this->identifier ?? get_class($this);
    }
}