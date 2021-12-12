<?php

namespace Appkeep\Eye;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Checklist extends Collection
{
    public static function build(array $checklist)
    {
        $items = [];

        foreach ($checklist['checks'] as $check) {
            $items[] = Check::build($check);
        }

        return new static($items);
    }

    public static function fetch()
    {
        $url = sprintf('%s/api/eye/checklist', config('appkeep.url'));

        $response = Http::withHeaders([
            'Authorization' => 'EyeSecret ' . config('appkeep.secret'),
        ])
            ->get($url)
            ->throw()
            ->json();

        return self::build($response);
    }
}
