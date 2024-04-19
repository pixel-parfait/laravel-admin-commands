<?php

namespace PixelParfait\LaravelAdminCommands;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PixelParfait\LaravelAdminCommands\Skeleton\SkeletonClass
 */
class LaravelAdminCommandsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin-commands';
    }
}
