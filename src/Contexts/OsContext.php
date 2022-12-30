<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class OsContext implements Arrayable
{
    public function toArray()
    {
        return [
            'name' => php_uname('s'),
            'kernel' => php_uname('a'),
            'build' => php_uname('v'),
        ];
    }
}
