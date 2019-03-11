<?php
declare(strict_types=1);

namespace Eagle\DI;

use Eagle\DI\Definition\DefinitionInterface;
use Psr\Container\ContainerInterface;
use Eagle\DI\Definition\DefinitionCollection;
use Eagle\DI\ServiceProvider\ServiceProviderDispatcher;
use Eagle\DI\Exception\NotFoundException;

class Container implements ContainerInterface {

    protected $definitionCollection;

    protected $serviceProvider;

    protected $shared = false;

    protected $warpContainer;

    public function __construct()
    {
        $this->definitionCollection = new DefinitionCollection;
        $this->serviceProvider = new ServiceProviderDispatcher;

        if ($this->definitionCollection instanceof ContainerSourceInterface) {
            $this->definitionCollection->setContainer($this);
        }

        if ($this->serviceProvider instanceof ContainerSourceInterface) {
            $this->serviceProvider->setContainer($this);
        }
    }

    /**
     * 添加要解析的包含依赖关系的对象
     *
     * @param string $id
     * @param null $value
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function set(string $id, $value = null, bool $shared = false) : DefinitionInterface
    {
        $value = $value ?? $id;
        $shared = $shared ?? $this->shared;

        return $this->definitionCollection->set($id, $value, $shared);
    }

    /**
     * 获取要解析的包含依赖关系的对象
     *
     * @param string $id
     * @param bool $isNew
     * @return mixed
     */
    public function get($id, bool $isNew = false)
    {
        // TODO: Implement get() method.
        if ($this->warpContainer) {
            $resoled = $this->warpContainer->get($id);
            return $resoled;
        }

        if (isset($this->definitionCollection[$id])) {
            $resoled = $this->definitionCollection->resolve($id, $isNew);
            return $resoled;
        }

        //判断是否已被添加到providers属性数组，然后注册服务
        if ($this->serviceProvider->provides($id)) {
            $this->serviceProvider->register($id);
            return $this->get($id, $isNew);
        }

        throw new NotFoundException(sprintf('Name (%s) is not being managed by the container or delegates', $id));
    }

    /**
     * 判断
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        // TODO: Implement has() method.
        if (isset($this->warpContainer) && $this->warpContainer->has($id)) {
            return true;
        }

        if (isset($this->definitionCollection[$id])) {
            return true;
        }

        if ($this->serviceProvider->provides($id)) {
            return true;
        }

        return false;
    }

    /**
     * 自动布线
     * @param \Psr\Container\ContainerInterface $container
     * @return self
     */
    public function autoWiring(ContainerInterface $container) : self
    {
        $this->warpContainer = $container;

        if ($container instanceof ContainerSourceInterface) {
            $container->setContainer($this);
        }

        return $this;
    }

    /**
     * 新添加一个服务提供者
     * @param $provider
     * @return Container
     */
    public function addServiceProvider($provider) : self
    {
        $this->serviceProvider->add($provider);

        return $this;
    }
}