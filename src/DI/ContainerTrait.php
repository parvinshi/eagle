<?php
declare(strict_types=1);

namespace Eagle\DI;

use Psr\Container\ContainerInterface;
use Eagle\DI\Exception\ContainerException;

trait ContainerTrait
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container) : ContainerSourceInterface
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer() : ContainerInterface
    {
        if ($this->container instanceof ContainerInterface) {
            return $this->container;
        }

        throw new ContainerException('No container implementation has been set.');
    }
}