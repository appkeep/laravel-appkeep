<?php

namespace Tests;

use Appkeep\Eye\Check;
use Appkeep\Eye\Result;

class TestCheck extends Check
{
    private $result;

    public function run()
    {
        if ($this->result) {
            return $this->result;
        }

        return Result::ok();
    }

    public function result(Result $result)
    {
        $this->result = $result;

        return $this;
    }
}
