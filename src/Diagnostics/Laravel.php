<?php

namespace Appkeep\Laravel\Diagnostics;

class Laravel
{
    public static function version()
    {
        return app()->version();
    }
}
