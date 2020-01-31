<?php

namespace Ndberg\LaravelPassportResourceServerMiddleware;

use Illuminate\Support\Facades\Facade;

/**
 *
 */
class LaravelPassportResourceServerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-passport-resource-server-middleware';
    }
}
