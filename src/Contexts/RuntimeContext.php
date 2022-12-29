<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class RuntimeContext implements Arrayable
{
    public function toArray()
    {
        return [
            'name' => 'php',
            'interface' => php_sapi_name(),
            'version' => join('.', [
                PHP_MAJOR_VERSION,
                PHP_MINOR_VERSION,
                PHP_RELEASE_VERSION,
            ]),
        ];
    }
}
