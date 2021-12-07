<?php

namespace Appkeep\Eye;

class Result
{
    public $output;

    public $message = null;

    public $passes = true;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public static function ok()
    {
        return new Result(true);
    }

    public static function output($output, $threshold = null)
    {
        $result = new Result($output);

        if (! is_null($threshold)) {
            $result->passes = $threshold->passes($output);
        }

        return $result;
    }

    public function withMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
