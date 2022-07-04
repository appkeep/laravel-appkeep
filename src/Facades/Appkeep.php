<?php

namespace Appkeep\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Appkeep extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'appkeep';
    }
}
