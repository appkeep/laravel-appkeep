<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class RequestContext implements Arrayable
{
    public function toArray()
    {
        if (! app()->runningUnitTests() && app()->runningInConsole()) {
            return [
                'command' => implode(' ', $_SERVER['argv']),
            ];
        }

        $request = request();

        return [
            'path' => '/' . ltrim($request->path(), '/'),
            'method' => $request->method(),
        ];
    }
}
