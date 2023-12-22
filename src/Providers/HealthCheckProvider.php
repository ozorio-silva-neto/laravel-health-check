<?php

namespace Ozoriotsn\HealthCheckCmd\Providers;

use Illuminate\Support\ServiceProvider;

class HealthCheckProvider extends ServiceProvider
{

    protected array $commands = [
        'Ozoriotsn\HealthCheckCmd\Commands\HealthCheck'
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
