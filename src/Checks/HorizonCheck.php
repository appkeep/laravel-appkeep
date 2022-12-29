<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Enums\Scope;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HorizonCheck extends Check
{
    public $scope = Scope::GLOBAL;

    public function run()
    {
        $supervisors = app(MasterSupervisorRepository::class)->all();

        if (empty($supervisors)) {
            return Result::fail('Horizon not detected.')->summary('Not running');
        }

        $paused = array_filter(
            $supervisors,
            fn ($master) => $master->status === 'paused'
        );

        if (! empty($paused)) {
            return Result::warn('Horizon is paused.')->summary('Paused');
        }

        return Result::ok()->summary('Running');
    }
}
