<?php
declare(strict_types=1);

namespace Eagle\DI\ServiceProvider;

use Eagle\DI\ContainerSourceInterface;

interface ServiceProviderInterface extends ContainerSourceInterface
{
    /**
     * 判断该服务是否已添加
     * @param string $service
     * @return bool
     */
    public function provides(string $service) : bool;

    /**
     * 注册已添加定义的服务
     * @return mixed
     */
    public function register();

    /**
     * @param string $id
     * @return ServiceProviderInterface
     */
    public function setIdentifier(string $id) : self;

    /**
     * @return string
     */
    public function getIdentifier() : string;
}