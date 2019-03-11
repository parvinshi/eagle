<?php
declare(strict_types=1);

namespace Eagle\Route;


class Route
{
    /** @var string */
    public $httpMethod;

    /** @var @var string */
    public $regex;

    /** @var mixed */
    public $handler;

    /** @var array */
    public $variables = [];

    public function __construct(string $httpMethod = 'GET', string $regex = '/', array $variables = [], $handler = null)
    {
        $this->httpMethod = $httpMethod;
        $this->regex = $regex;
        $this->variables = $variables;
        $this->handler = $handler;
    }

    public function isMatched(string $path) : bool
    {
        $regex = '~^' . $this->regex . '$~';
        return (bool) preg_match($regex, $path);
    }
}