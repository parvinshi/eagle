<?php
declare(strict_types=1);

namespace Eagle\Route\Dispatcher;

interface RouteDispatcherInterface
{
    /** @var int 路由解析后找不到 */
    const NOT_FOUND = 0;

    /** @var int 路由解析后被匹配到 */
    const FOUND = 1;

    /** @var int 不被允许的http请求方法 */
    const METHOD_NOT_ALLOWED = 2;

    /**
     * 调度路由
     * @param $httpMethod
     * @param $uri
     * @return mixed
     */
    public function dispatch($httpMethod, $uri);
}