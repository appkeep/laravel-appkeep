<?php

namespace Appkeep\Laravel\Health\Checks;

use Appkeep\Laravel\Health\Check;
use Appkeep\Laravel\Health\Result;

class HorizonCheck extends Check
{
    public function run()
    {
        try {
            $horizon = app('Laravel\Horizon\Contracts\MasterSupervisorRepository');
        } catch (\Exception $e) {
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
