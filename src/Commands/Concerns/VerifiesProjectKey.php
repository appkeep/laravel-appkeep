<?php

namespace Appkeep\Laravel\Commands\Concerns;

use Illuminate\Support\Facades\Http;

trait VerifiesProjectKey
{
    public function verifyProjectKey($key)
    {
        $status = Http::withHeaders(['Authorization' => 'Bearer ' . $key])
            ->post(config('appkeep.endpoint'), [])
            ->status();

        return $status === 200;
    }
}
