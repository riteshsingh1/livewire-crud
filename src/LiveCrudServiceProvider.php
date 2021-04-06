<?php

namespace Imritesh\LiveCrud;

use Illuminate\Support\ServiceProvider;
use Imritesh\LiveCrud\Commands\Crud;
use Imritesh\LiveCrud\Commands\LiveCrudView;

class LiveCrudServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/livecrud.php', 'livecrud');

        // Register the service the package provides.
        $this->app->singleton('livecrud', function ($app) {
            return new LiveCrud;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['livecrud'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/livecrud.php' => config_path('livecrud.php'),
        ], 'livecrud.config');

        // Registering package commands.
        $this->commands([
            Crud::class,
            LiveCrudView::class
        ]);
    }
}
