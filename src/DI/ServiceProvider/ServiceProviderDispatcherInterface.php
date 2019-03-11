<?php
declare(strict_types=1);

namespace Eagle\DI\ServiceProvider;

use Eagle\DI\ContainerSourceInterface;

interface ServiceProviderDispatcherInterface extends ContainerSourceInterface, \IteratorAggregate
{
    /**
     * 添加定义服务
     * @param $provider
     * @return ServiceProviderDispatcherInterface
     */
    public function add($provider) : ServiceProviderDispatcherInterface;

    /**
     * 判断该服务是否已添加
     * @param string $service
     * @return bool
     */
    public function provides(string $service) : bool;

    /**
     * 注册已添加定义的服务
     * @param string $service
     * @return mixed
     */
    public function register(string $service);
}