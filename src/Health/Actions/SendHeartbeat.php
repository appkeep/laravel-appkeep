<?php

namespace Appkeep\Laravel\Health\Actions;

use Illuminate\Support\Collection;
use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;
use Appkeep\Laravel\Health\Diagnostics\Git;
use Appkeep\Laravel\Health\Diagnostics\Server;
use Appkeep\Laravel\Health\Diagnostics\Laravel;

class SendHeartbeat
{
    public function __invoke(Collection $checkResults)
    {
        Http::withHeaders($this->requestHeaders())
            ->post(
                config('appkeep.endpoint'),
                $this->requestBody($checkResults)
            )
            ->throw();
    }

    private function requestHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => sprintf(
                'Bearer %s',
                config('appkeep.key')
            ),
        ];
    }

    private function requestBody(Collection $checkResults): array
    {
        return [
            'server' => [
                'uid' => Server::uniqueIdentifier(),
                'name' => Server::name(),
                'os' => Server::os(),
            ],
            'packages' => [
                'laravel/framework' => Laravel::version(),
                'appkeep/laravel-appkeep' => Appkeep::version(),
            ],
            'git' => ! ($hash = Git::shortCommitHash()) ? null : [
                'commit' => $hash,
                'url' => Git::repositoryUrl(),
            ],
            'checks' => $checkResults->toArray(),
        ];
    }
}
