<?php
declare(strict_types=1);

namespace Eagle\Route\RouteParse;

use Eagle\Route\Exception\BadRouteException;

class RouteParse implements RouteParseInterface
{
    const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}\??
REGEX;
    const DEFAULT_DISPATCH_REGEX = '[^/]+';

    protected $regexOffset = 0;

    protected $partsCounter = 0;

    protected $parts = [];

    protected $regexShortcuts = [
        ':n' => ':[0-9]+', //number
        ':w' => ':[a-zA-Z]+', //word
        ':c' => ':[a-zA-Z0-9+_\-\.]+' //alphanum_dash
    ];

    /**
     * 路由解析格式 "/user/{name}/{id:[0-9]+}?"
     * @param string $route
     * @return array
     */
    public function parse(string $route) : array
    {
        // TODO: Implement parse() method.
        $routeStr = '';
        $routeData = [];

        $segments = $this->parseOptionals($route);
        foreach ($segments as $n => $segment) {
            if ($segment === '' && $n !== 0) {
                throw new BadRouteException('Empty optional part');
            }

            $routeStr .= $segment;
            $routeData[] = $this->parsePlaceholders($routeStr);
        }

        return $routeData;
    }

    /**
     * 解析路由中的占位符变量
     * @param string $route
     * @return mixed
     */
    private function parsePlaceholders(string $route) : array
    {
        $route = strtr($route, $this->regexShortcuts);

        if (!preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            return [$route];
        }

        $offset = 0;
        $routeData = [];
        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }

            // $set[2]占位符匹配段包含pattern
            $regexPart = isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_DISPATCH_REGEX;

            $routeData[] = [
                $set[1][0],
                $regexPart
            ];

            $this->regexOffset = $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset !== strlen($route)) {
            $routeData[] = substr($route, $offset);
        }

        return $routeData;
    }

    /**
     * 解析路由中可选部分
     * @param string $route
     * @return array
     */
    private function parseOptionals(string $route) : array
    {
        $routeWithoutClosing = rtrim($route, ']');
        $numOptionals = strlen($route) - strlen($routeWithoutClosing);
        $pattern = '~' . self::VARIABLE_REGEX . '(*SKIP)(*F)';

        $segments = preg_split($pattern . ' | \[~x', $routeWithoutClosing);
        if ($numOptionals !== count($segments) - 1) {
            if (preg_match($pattern . ' | \]~x', $routeWithoutClosing)) {
                throw new BadRouteException('Optional segments can only occur at the end of a route');
            }
            throw new BadRouteException("Number of opening '[' and closing ']' does not match");
        }

        return (array) $segments;
    }

    /**
     * @param string $route
     * @param int $nextOffset
     */
    protected function staticParts(string $route, int $nextOffset)
    {
        $parts = preg_split('~(/)~u', substr($route, $this->regexOffset, $nextOffset - $this->regexOffset), 0, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $part) {
            if ($part) {
                $part = $this->quote($part);
                $this->parts[$this->partsCounter] = $part;

                $this->partsCounter++;
            }
        }
    }

    /**
     * @param string $match
     * @return string
     */
    protected function makeOptional(string $match)
    {
        $pre = $this->partsCounter - 1;

        if (isset($this->parts[$pre]) && $this->parts[$pre] === '/') {
            $this->partsCounter--;
            $match = '(?:/' . $match . ')';
        }

        return $match . '?';
    }

    protected function quote($part)
    {
        return preg_quote($part, '~');
    }
}