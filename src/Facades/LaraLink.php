<?php

namespace LaraLink\Facades;

use Illuminate\Support\Facades\Facade;

class LaraLink extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laralink';
    }
}
