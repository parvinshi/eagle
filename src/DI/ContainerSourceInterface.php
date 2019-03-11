<?php
declare(strict_types=1);

namespace Eagle\DI;

use Psr\Container\ContainerInterface;

interface ContainerSourceInterface
{

    /**
     * 设置传递容器实例
     *
     * @param ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container) : self;

    /**
     * 获取已设置的容器实例
     *
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface;
}