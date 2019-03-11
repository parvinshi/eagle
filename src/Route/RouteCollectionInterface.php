<?php
declare(strict_types=1);

namespace Eagle\Route;


interface RouteCollectionInterface
{
    /**
     * 添加一个或多个路由
     * @param string|array $httpMethod
     * @param string $path
     * @param $handler
     * @return void
     */
    public function addRoute($httpMethod, string $path, $handler);

    /**
     * 为多个路由添加为一个组
     * @param string $prefix
     * @param $callable
     * @return mixed
     */
    public function addGroup(string $prefix, callable $callable);

    /**
     * @return array
     */
    public function getRouteData() : array;

    /**
     * 定义一个get请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function get(string $path, $handler);

    /**
     * 定义一个post请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function post(string $path, $handler);

    /**
     * 定义一个put请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function put(string $path, $handler);

    /**
     * 定义一个patch请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function patch(string $path, $handler);

    /**
     * 定义一个delete请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function delete(string $path, $handler);

    /**
     * 定义一个head请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function head(string $path, $handler);

    /**
     * 定义一个options请求路由
     * @param string $path
     * @param $handler
     * @return void
     */
    public function options(string $path, $handler);
}