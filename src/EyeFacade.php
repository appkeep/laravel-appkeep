<?php

namespace Appkeep\Eye;

use Illuminate\Support\Facades\Facade;

class AppkeepFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'appkeep-eye';
    }
}
