<?php

namespace Appkeep\Eye;

class Threshold
{
    public $threshold;
    public $comparator;

    public function __construct($threshold, $comparator)
    {
        $this->threshold = $threshold;
        $this->comparator = $comparator;
    }

    public function passes($value)
    {
        // checks value against threshold usng the comparator
        switch ($this->comparator) {
            case '=':
                return $value == $this->threshold;
            case '!=':
                return $value != $this->threshold;
            case '>':
                return $value > $this->threshold;
            case '>=':
                return $value >= $this->threshold;
            case '<':
                return $value < $this->threshold;
            case '<=':
                return $value <= $this->threshold;
            default:
                throw new \RuntimeException(sprintf(
                    'Appkeep Eye: Invalid comparator %s in checklist, must be one of >, <, >=, <=, ==, !=',
                    $this->comparator
                ));
        }
    }
}
