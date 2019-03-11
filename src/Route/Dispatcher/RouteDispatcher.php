<?php
declare(strict_types=1);

namespace Eagle\Route\Dispatcher;


class RouteDispatcher implements RouteDispatcherInterface
{
    protected $staticRouteMap = [];

    protected $variableRouteData = [];

    public function __construct($data)
    {
        list($this->staticRouteMap, $this->variableRouteData) = $data;
    }

    /**
     * 调度路由
     *
     * @param $httpMethod
     * @param $uri
     * @return array
     */
    public function dispatch($httpMethod, $uri)
    {
        // TODO: Implement dispatcher() method.
        $staticRouteMap = $this->staticRouteMap;
        $varRouteData = $this->variableRouteData;
        if (isset($staticRouteMap[$uri][$httpMethod])) {
            $handler = $this->staticRouteMap[$uri][$httpMethod];
            return [self::FOUND, $handler, []];
        }

        if (isset($varRouteData[$httpMethod])) {
            $result = $this->dispatchVariableRoute($varRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        if (isset($staticRouteMap['*'][$uri])) {
            $handler = $staticRouteMap['*'][$uri];
            return [self::FOUND, $handler, []];
        }

        if (isset($varRouteData['*'])) {
            $result = $this->dispatchVariableRoute($varRouteData['*'], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        $allowedMethods = [];
        foreach ($staticRouteMap as $uri => $mapData) {
            if (array_keys($mapData)[0] !== $httpMethod && isset($staticRouteMap[$uri])) {
                $allowedMethods[] = array_keys($mapData)[0];
            }
        }

        foreach ($varRouteData as $method => $routeData) {
            if ($method === $httpMethod) {
                continue;
            }

            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        if ($allowedMethods) {
            return [self::METHOD_NOT_ALLOWED, $allowedMethods];
        }

        return [self::NOT_FOUND];
    }

    /**
     * 调度动态包含变量参数的路由
     *
     * @param $routeData
     * @param $uri
     * @return array
     */
    protected function dispatchVariableRoute($routeData, $uri)
    {
        foreach ($routeData as $data) {
            if (!preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            list($handler, $varNames) = $data['routeMap'][count($matches)];

            $vars = [];
            $i = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            return [self::FOUND, $handler, $vars];
        }

        return [self::NOT_FOUND];
    }

    public function handle()
    {

    }
}