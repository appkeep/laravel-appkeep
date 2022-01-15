<?php

namespace Appkeep\Eye;

use Appkeep\Eye\Enums\Status;

class Result
{
    /**
     * Outcome value...
     * Can be one of the status constants.
     */
    public $value;

    /**
     * A message describing the warning or error.
     */
    public $message = null;

    /**
     * A short value for the result.
     */
    public $summary = null;

    public function __construct($value, $message = null)
    {
        $this->value = $value;
        $this->message = $message;
    }

    public static function ok()
    {
        return new Result(Status::OK);
    }

    public static function warn($message)
    {
        return new Result(Status::WARN, $message);
    }

    public static function fail($message)
    {
        return new Result(Status::FAIL, $message);
    }

    public static function crash($message)
    {
        return new Result(Status::CRASH, $message);
    }

    public function meta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function summary($summary)
    {
        $this->summary = $summary;

        return $this;
    }
}
