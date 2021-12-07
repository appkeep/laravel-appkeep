<?php

namespace Appkeep\Eye\Checks;

use Illuminate\Support\Arr;
use Appkeep\Eye\ChecklistItem;

abstract class AbstractCheck
{
    /**
     * @var ChecklistItem
     */
    protected $checklistItem;

    public $result = null;

    public function __construct(ChecklistItem $checklistItem)
    {
        $this->checklistItem = $checklistItem;
    }

    abstract public function run();

    /**
     * This can be used in the check to retrieve arguments.
     * Arguments can be managed in Appkeep UI.
     */
    protected function argument($key, $default = null)
    {
        return Arr::get($this->checklistItem->arguments, $key, $default);
    }
}
