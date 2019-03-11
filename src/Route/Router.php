<?php
declare(strict_types=1);
namespace Eagle\Route;

use Eagle\Route\Dispatcher\RouteDispatcher;

class Router
{
    protected $route;

    protected $routeCollection;

    protected $routeDispatcher;

    protected $routeHandler;

    protected $routeFactory;

    protected $routeCollectionFactory;

    protected $routeDispatcherFactory;

    protected $routeHandlerFactory;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->setRouteFactory([$this, 'routeFactory']);
        $this->setRouteCollectionFactory([$this, 'routeCollectionFactory']);
        $this->setRouteDispatcherFactory([$this, 'routeDispatcherFactory']);
        $this->setRouteHandlerFactory([$this, 'routeHandlerFactory']);
    }

    /**
     * 添加定义路由
     * @param $httpMethod
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $this->getRouteCollection();
        if (is_callable([$this->routeCollection, $name])) {
            call_user_func_array([$this->routeCollection, $name], $arguments);
        }

        return $this;
    }

    /**
     * @param callable $routeFactory
     */
    public function setRouteFactory(callable $routeFactory)
    {
        $this->routeFactory = $routeFactory;
    }

    /**
     * @param callable $routeCollectionFactory
     */
    public function setRouteCollectionFactory(callable $routeCollectionFactory)
    {
        $this->routeCollectionFactory = $routeCollectionFactory;
    }

    /**
     * @param callable $routeDispatcherFactory
     */
    public function setRouteDispatcherFactory(callable $routeDispatcherFactory)
    {
        $this->routeDispatcherFactory = $routeDispatcherFactory;
    }

    public function setRouteHandlerFactory(callable $routeHandlerFactory)
    {
        $this->routeHandlerFactory = $routeHandlerFactory;
    }

    /**
     * 路由实例工厂
     * @return Route
     */
    protected function routeFactory() : Route
    {
        return new Route();
    }

    /**
     * 路由集合工厂
     * @return RouteCollection
     */
    protected function routeCollectionFactory() : RouteCollection
    {
        return new RouteCollection($this->getRoute());
    }

    /**
     * 路由调度器工厂
     * @return RouteDispatcher
     */
    protected function routeDispatcherFactory() : RouteDispatcher
    {
        return new RouteDispatcher($this->getRouteData());
    }

    /**
     * 获取路由调度器然后执行路由回调
     * @return RouteHandler
     */
    protected function routeHandlerFactory() : RouteHandler
    {
        return new RouteHandler($this->getRouteDispatcher());
    }

    /**
     * 获取路由实例
     * @return Route
     */
    public function getRoute() : Route
    {
        if (!$this->route) {
            $this->route = call_user_func($this->routeFactory);
        }

        return $this->route;
    }

    /**
     * 获取路由集合对象
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection
    {
        if (!$this->routeCollection) {
            $this->routeCollection = call_user_func($this->routeCollectionFactory);
        }

        return $this->routeCollection;
    }

    /**
     * 获取路由调度器对象
     * @return RouteDispatcher
     */
    public function getRouteDispatcher() : RouteDispatcher
    {
        if (!$this->routeDispatcher) {
            $this->routeDispatcher = call_user_func($this->routeDispatcherFactory);
        }

        return $this->routeDispatcher;
    }

    /**
     * 获取路由执行对象
     * @return RouteHandler
     */
    public function getRouteHandler() : RouteHandler
    {
        if (!$this->routeHandler) {
            $this->routeHandler = call_user_func($this->routeHandlerFactory);
        }

        return $this->routeHandler;
    }

    /**
     * 获取解析后的路由数据
     * @return array
     */
    public function getRouteData() : array
    {
       $data = $this->routeCollection->getRouteData();
       return $data;
    }
}