<?php

namespace Appkeep\Eye;

use Illuminate\Support\Collection;

class Checklist extends Collection
{
    public static function fromJson($json)
    {
        $items = [];

        $json = json_decode($json, true);

        foreach ($json['checks'] as $check) {
            $items[] = ChecklistItem::build($check);
        }

        return new static($items);
    }
}
