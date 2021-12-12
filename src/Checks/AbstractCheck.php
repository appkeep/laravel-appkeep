<?php

namespace Appkeep\Eye\Checks;

use Appkeep\Eye\Check;
use Illuminate\Support\Arr;

abstract class AbstractCheck
{
    /**
     * @var Check
     */
    protected $check;

    public $result = null;

    public function __construct(Check $check)
    {
        $this->check = $check;
    }

    abstract public function run();

    /**
     * This can be used in the check to retrieve arguments.
     * Arguments can be managed in Appkeep UI.
     */
    protected function argument($key, $default = null)
    {
        return Arr::get($this->check->arguments, $key, $default);
    }
}
