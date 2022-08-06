<?php

namespace Appkeep\Laravel\Health\Diagnostics;

class Laravel
{
    public static function version()
    {
        return app()->version();
    }
}
