<?php

namespace PixelParfait\LaravelAdminCommands;

use Illuminate\Support\ServiceProvider;
use PixelParfait\LaravelAdminCommands\Commands\CreateResource;

class LaravelAdminCommandsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateResource::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton('laravel-admin-commands', function () {
            return new LaravelAdminCommands;
        });
    }
}
