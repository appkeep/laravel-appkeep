<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class OsContext implements Arrayable
{
    public function toArray()
    {
        return [
            'name' => php_uname('s'),
            'kernel' => sprintf('%s %s', php_uname('r'), php_uname('m')),
            'distro' => $this->getDistro(),
        ];
    }

    private function getDistro()
    {
        return rescue(
            fn () => @parse_ini_string(shell_exec('cat /etc/lsb-release 2> /dev/null'))['DISTRIB_DESCRIPTION'],
            fn () => null
        ) ?? null;
    }
}
