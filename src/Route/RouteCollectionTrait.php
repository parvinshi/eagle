<?php
declare(strict_types=1);

namespace Eagle\Route;

/**
 * Implements RouteCollectionInterface
 * Trait RouteCollectionTrait
 * @package Eagle\Route
 */
trait RouteCollectionTrait
{
    /**
     * {@inheritdoc}
     */
    abstract public function addRoute($httpMethod, string $path, $handler);

    /**
     * {@inheritdoc}
     */
    abstract public function addGroup(string $prefix, $handler);

    /**
     * {@inheritdoc}
     */
    public function group(string $prefix, callable $callable) {
        return $this->addGroup($prefix, $callable);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $path, $handler)
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $path, $handler)
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $path, $handler)
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $path, $handler)
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $path, $handler) : Route
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function head(string $path, $handler)
    {
        return $this->addRoute('HEAD', $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function options(string $path, $handler)
    {
        return $this->addRoute('OPTIONS', $path, $handler);
    }
}