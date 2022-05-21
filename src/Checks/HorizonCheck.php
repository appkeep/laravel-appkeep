<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HorizonCheck extends Check
{
    public function run()
    {
        try {
            $horizon = app(MasterSupervisorRepository::class);
        } catch (Exception) {
            return Result::fail('Horizon not detected.');
        }

        $masterSupervisors = $horizon->all();

        if (count($masterSupervisors) === 0) {
            return Result::fail('Horizon is not running.')
                ->summary('Not running');
        }

        $masterSupervisor = $masterSupervisors[0];

        if ($masterSupervisor->status === 'paused') {
            return Result::warn('Horizon is paused.')
                ->summary('Paused');
        }

        return Result::ok()->summary('Running');
    }
}
