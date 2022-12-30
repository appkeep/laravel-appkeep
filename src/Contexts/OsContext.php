<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class OsContext implements Arrayable
{
    public function toArray()
    {
        return [
            'name' => php_uname('s'),
            'kernel' => php_uname('a'),
            'distro' => $this->getDistro(),
        ];
    }

    private function getDistro()
    {
        return rescue(function () {
            return @parse_ini_string(shell_exec('cat /etc/lsb-release 2> /dev/null'))['DISTRIB_DESCRIPTION'];
        }, function ($e) {
            return php_uname('v');
        });
    }
}
