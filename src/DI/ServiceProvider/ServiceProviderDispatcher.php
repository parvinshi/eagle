<?php
declare(strict_types=1);

namespace Eagle\DI\ServiceProvider;

use Eagle\DI\ContainerSourceInterface;
use Eagle\DI\ContainerTrait;
use Eagle\DI\Exception\ContainerException;

class ServiceProviderDispatcher implements ServiceProviderDispatcherInterface
{
    use ContainerTrait;

    protected $providers = [];

    protected $registered = [];

    /**
     * @param $provider
     * @return ServiceProviderDispatcherInterface
     */
    public function add($provider): ServiceProviderDispatcherInterface
    {
        // TODO: Implement add() method.
        if (is_string($provider) && $this->getContainer()->has($provider)) {
            $provider = $this->getContainer()->get($provider);
        } elseif (is_string($provider) && class_exists($provider)) {
            $provider = new $provider;
        }

        if ($provider instanceof ContainerSourceInterface) {
            $provider->setContainer($this->getContainer());
        }

        if ($provider instanceof ServiceProviderBootInterface) {
            $provider->boot();
        }

        if ($provider instanceof ServiceProviderInterface) {
            $this->providers[] = $provider;

            return $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides(string $service): bool
    {
        // TODO: Implement provides() method.
        foreach ($this->getIterator() as $provider) {
            if ($provider->provides($service)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $service)
    {
        // TODO: Implement register() method.
        if (!$this->provides($service)) {
            throw new ContainerException(sprintf('(%s) is not provided by a service provider', $service));
        }

        array_walk($this->getIterator(), function($provider) use($service) {
            if (!isset($this->registered[$provider->getIdentifier()]) && $provider->provides($service)) {
                $provider->register();
                $this->registered[] = $provider->getIdentifier();
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : \Generator
    {
        // TODO: Implement getIterator() method.
        $count = count($this->providers);

        for ($i = 0; $i < $count; $i++) {
            yield $this->providers[$i];
        }
    }
}