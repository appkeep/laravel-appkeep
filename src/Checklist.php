<?php

namespace Appkeep\Eye;

use Illuminate\Support\Collection;

class Checklist extends Collection
{
    public static function fromJson($json)
    {
        $items = [
            static::getDefaultCheck(),
        ];

        $json = json_decode($json, true);

        foreach ($json['checks'] as $check) {
            $item = new ChecklistItem();
            $item->check = $check['check'];
            $item->frequency = $check['frequency'];
            $item->arguments = $check['arguments'] ?? [];

            if (isset($check['threshold'])) {
                $item->threshold = new Threshold(
                    $check['threshold']['value'],
                    $check['threshold']['comparator']
                );
            }

            $items[] = $item;
        }

        return new static($items);
    }

    protected static function getDefaultCheck()
    {
        return new ChecklistItem([
            'check' => 'heartbeat',
            'frequency' => '* * * * *',
            'arguments' => [],
        ]);
    }
}
