<?php

namespace Appkeep\Laravel\Events;

use RuntimeException;
use Appkeep\Laravel\Contexts\OsContext;
use Appkeep\Laravel\Contexts\ServerContext;
use Illuminate\Contracts\Support\Arrayable;
use Appkeep\Laravel\Contexts\RuntimeContext;

abstract class AbstractEvent
{
    protected $name;

    private $contexts = [];

    public function __construct()
    {
        $this->setDefaultContexts();
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'context' => $this->contexts,
        ];
    }

    public function setContext($name, $data)
    {
        if (isset($this->contexts[$name])) {
            throw new RuntimeException("Context '${name}' is already set. You cannot override this context.");
        }

        if (! is_string($data) && ! is_array($data) && ! ($data instanceof Arrayable)) {
            throw new RuntimeException(
                "Data passed for ${name} context must be either a string,"
                    + " an array or an Arrayable class instance."
            );
        }

        $this->contexts[$name] = $data instanceof Arrayable
            ? $data->toArray()
            : $data;

        return $this;
    }

    protected function setDefaultContexts()
    {
        $this->setContext('os', new OsContext)
            ->setContext('runtime', new RuntimeContext)
            ->setContext('server', new ServerContext);
    }
}
