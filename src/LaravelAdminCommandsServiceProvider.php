<?php

namespace PixelParfait\LaravelAdminCommands;

use Illuminate\Support\ServiceProvider;
use PixelParfait\LaravelAdminCommands\Commands\CreateController;
use PixelParfait\LaravelAdminCommands\Commands\CreatePages;

class LaravelAdminCommandsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatePages::class,
                CreateController::class,
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
