<?php

namespace Appkeep\Laravel;

use Appkeep\Laravel\Enums\Status;

class Result
{
    /**
     * Can be one of the status constants.
     */
    public $status;

    /**
     * A message describing the warning or error.
     */
    public $message = null;

    /**
     * A short value for the result.
     */
    public $summary = null;

    /**
     * For details like actual numbers, etc.
     */
    public $meta = null;

    public function __construct($status, $message = null)
    {
        $this->status = $status;
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

    public function status($status)
    {
        $this->status = $status;

        return $this;
    }

    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    public function summary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    public function failWith($message)
    {
        return $this->status(Status::FAIL)->message($message);
    }
}
