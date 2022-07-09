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

        $output = shell_exec('/usr/lib/update-notifier/apt-check');
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            if (strpos($line, 'security') !== false) {
                // extract number from text
                $number = preg_replace('/[^0-9]/', '', $line);

                return Result::warn('You have ' . $number . ' pending security updates for Ubuntu')
                    ->summary($number);
            }
        }

        return Result::ok();
    }
}
