<?php
declare(strict_types=1);
namespace Eagle\Route\RouteParse;

interface RouteParseInterface
{
    /**
     * 解析一个路由字符串为包含路由数据的数组
     * @param string $route
     * @return mixed
     */
    public function parse(string $route);
}