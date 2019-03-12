<?php
declare(strict_types=1);

namespace Eagle\DI;

use Psr\Container\ContainerInterface;

class ContainerBuilder
{
    /** @var string  */
    private $containerClass;

    /** @var bool  */
    private $useAutoWiring = true;

    /** @var AutoWiringContainer  */
    private $warpContainer;

    /**
     * ContainerBuilder constructor.
     * @param string $containerClass
     * @param ContainerInterface|null $warpContainer
     */
    public function __construct(string $containerClass = 'Eagle\DI\Container', ContainerInterface $warpContainer = null)
    {
        $this->containerClass = $containerClass;
        $this->warpContainer = $warpContainer ? new $warpContainer : new AutoWiringContainer;
    }

    /**
     * auto wiring 构建自动装配
     *
     * @return mixed
     */
    public function build()
    {
        $container = new $this->containerClass;
        if ($this->useAutoWiring && $this->warpContainer) {
            $container->autoWiring($this->warpContainer);
        }

        return $container;
    }
}