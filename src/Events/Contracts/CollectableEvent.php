<?php

namespace Appkeep\Laravel\Events\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface CollectableEvent extends Arrayable
{
    public function dedupeHash(): string;
}
