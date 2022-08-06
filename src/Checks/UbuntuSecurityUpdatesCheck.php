<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Diagnostics\Server;

class UbuntuSecurityUpdatesCheck extends Check
{
    public static function make($name = null)
    {
        return (new UbuntuSecurityUpdatesCheck($name))->daily();
    }

    public function run()
    {
        if (! Server::isUbuntu()) {
            throw new Exception('This check can only be run on Ubuntu servers.');
        }

        // apt-check writes to stderr, so capture it with 2>&1
        $output = shell_exec('/usr/lib/update-notifier/apt-check 2>&1');

        list($updates, $securityUpdates) = array_map(
            'intval',
            explode(";", $output)
        );

        if ($securityUpdates > 0) {
            $message = sprintf('You have %d pending security updates for Ubuntu', $securityUpdates);

            return Result::warn($message)->summary($securityUpdates);
        }

        return Result::ok();
    }
}
