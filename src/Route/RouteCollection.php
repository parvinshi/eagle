<?php
declare(strict_types=1);

namespace Eagle\Route;

use Eagle\Route\RouteParse\RouteParse;
use Eagle\Route\Exception\BadRouteException;

class RouteCollection implements RouteCollectionInterface, \IteratorAggregate
{
    use RouteCollectionTrait;

    /** @var array 包含变量参数的路由 */
    protected $routes = [];

    /** @var array 不包含变量参数的路由 */
    protected $staticRoutes = [];

    protected $route;

    protected $routeParse;

    /** @var string 路由组前缀 */
    protected $groupPrefix = '';

    public $httpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    const GROUP_COUNT_APPROX_CHUNK_SIZE = 10;

    public function __construct(Route $route)
    {
        $this->route = $route ?? new Route;
        $this->routeParse = new RouteParse;
    }

    /**
     * 添加定义的路由
     * @param array|string $httpMethod
     * @param string $route
     * @param $handler
     */
    public function addRoute($httpMethod, string $route, $handler)
    {
        // TODO: Implement addRoute() method.
        $route = $this->groupPrefix . $route;
        $routeData = $this->routeParse->parse($route);

        foreach ((array) $httpMethod as $method) {
            foreach ($routeData as $data) {
                if ($this->isStaticRoute($data)) {
                    $this->addStaticRoute($method, $data, $handler);
                } else {
                    $this->addVariableRoute($method, $data, $handler);
                }
            }
        }
    }

    /**
     * 为一组路由添加组前缀
     *
     * @param string $prefix
     * @param callable $callback
     * @return mixed|void
     */
    public function addGroup(string $prefix, callable $callback)
    {
        $this->groupPrefix .= $prefix;
        $callback($this);
        $this->groupPrefix = '';
    }

    /**
     *判断路由是否是静态不包含变量参数
     *
     * @param $routeData
     * @return bool
     */
    protected function isStaticRoute($routeData)
    {
        return count($routeData) === 1 && is_string($routeData[0]);
    }

    /**
     * 添加静态不包含变量参数的路由
     *
     * @param string $httpMethod
     * @param array $routeData
     * @param $handler
     */
    protected function addStaticRoute(string $httpMethod, array $routeData, $handler)
    {
        $route = $routeData[0];

        if (isset($this->staticRoutes[$route][$httpMethod])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $route, $httpMethod
            ));
        }

        if ($this->exists($route)) {
            throw new BadRouteException(sprintf(
                'Static route "%s" is shadowed by previously defined variable route "%s" for method "%s"',
                $route, $route->regex, $httpMethod
            ));
        }

        $this->staticRoutes[$route][$httpMethod] = $handler;
    }

    /**
     * 添加动态包含变量参数的路由
     *
     * @param string $httpMethod
     * @param array $routeData
     * @param $handler
     */
    protected function addVariableRoute(string $httpMethod, array $routeData, $handler)
    {
        list($regex, $variables) = $this->buildRegexForRoute($routeData);

        if ($this->exists($regex)) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $regex, $httpMethod
            ));
        }

        $this->routes[$regex] = new Route($httpMethod, $regex, $variables, $handler);
    }

    /**
     * @param $routeData
     * @return array
     */
    private function buildRegexForRoute($routeData)
    {
        $regex = '';
        $variables = [];

        foreach ($routeData as $part) {
            if (is_string($part)) {
                $regex .= preg_quote($part, '~');
                continue;
            }

            list($name, $regexPart) = $part;

            if (isset($variables[$name])) {
                throw new BadRouteException(sprintf(
                    'Cannot use the same placeholder "%s" twice', $name
                ));
            }

            if ($this->regexHasCapturingGroups($regexPart)) {
                throw new BadRouteException(sprintf(
                    'Regex "%s" for parameter "%s" contains a capturing group',
                    $regexPart, $name
                ));
            }

            $variables[$name] = $name;
            $regex .=  '(' . $regexPart . ')';
        }

        return [$regex, $variables];
    }

    /**
     * @param string $regex
     * @return bool
     */
    private function regexHasCapturingGroups(string $regex) : bool
    {
        if (false === strpos($regex, '(')) {
            return false;
        }

        return (bool) preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    }

    /**
     * @return array
     */
    private function getVariableRouteData() : array
    {
        $data = [];
        $methodToRegexToRoutesMap = $this->getMethodToRegexToRoutesMap();
        foreach ($methodToRegexToRoutesMap as $method => $regexToRoutesMap) {
            $chunkSize = $this->computeChunkSize(count($regexToRoutesMap));
            $chunks = array_chunk($regexToRoutesMap, $chunkSize, true);
            $data[$method] = array_map([$this, 'processChunk'], $chunks);
        }

        return $data;
    }

    /**
     * @param int $count
     * @return int
     */
    private function computeChunkSize(int $count) : int
    {
        $numParts = max(1, round($count / self::GROUP_COUNT_APPROX_CHUNK_SIZE));

        return (int) ceil($count / $numParts);
    }

    /**
     * @param array $regexToRoutesMap
     * @return array
     */
    protected function processChunk(array $regexToRoutesMap)
    {
        $routeMap = [];
        $regexes = [];
        $numGroups = 0;

        foreach ($regexToRoutesMap as $regex => $route) {
            $numVariables = count($route->variables);
            $numGroups = max($numGroups, $numVariables);

            $regexes[] = $regex . str_repeat('()', $numGroups - $numVariables);
            $routeMap[$numGroups + 1] = [$route->handler, $route->variables];

            ++$numGroups;
        }

        $regex = '~^(?|' . implode('|', $regexes) . ')$~';
        return ['regex' => $regex, 'routeMap' => $routeMap];
    }

    /**
     * @return array
     */
    protected function getMethodToRegexToRoutesMap()
    {
        $mapData = [];

        foreach ($this->getIterator() as $regex => $route) {
            $mapData[$route->httpMethod][$regex] = $route;
        }

        return $mapData;
    }

    public function exists(string $path) : bool
    {
        foreach ($this->getIterator() as $route) {
            if ($path === $route->regex || $route->isMatched($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRouteData(): array
    {
        // TODO: Implement getRouteData() method.
        if (empty($this->routes)) {
            return [$this->staticRoutes, []];
        }

        return [$this->staticRoutes, $this->getVariableRouteData()];
    }

    /**
     * @return \Generator
     */
    public function getIterator() : \Generator
    {
        // TODO: Implement getIterator() method.
        return (function () {
            while(list($regex, $route) = each($this->routes)) {
                yield $regex => $route;
            }

            reset($this->routes);
        })();
    }
}