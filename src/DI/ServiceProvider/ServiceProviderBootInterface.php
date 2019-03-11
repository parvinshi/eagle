<?php
declare(strict_types=1);

namespace Eagle\DI\ServiceProvider;


interface ServiceProviderBootInterface extends ServiceProviderInterface
{
    /**
     * @return mixed
     */
    public function boot();
}