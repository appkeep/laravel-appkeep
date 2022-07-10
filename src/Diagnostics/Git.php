<?php

namespace Appkeep\Laravel\Diagnostics;

class Git
{
    public static function commitHash()
    {
        $output = shell_exec('cd ' . base_path() . ' && git rev-parse HEAD 2> /dev/null');

        if (! $output) {
            return null;
        }

        return $output;
    }

    public static function shortCommitHash()
    {
        if ($commitHash = self::commitHash()) {
            return substr($commitHash, 0, 7);
        }
    }

    public static function repositoryUrl()
    {
        $gitUrl = shell_exec('cd ' . base_path() . ' && git config --get remote.origin.url 2> /dev/null');

        // match repository name from remote origin url
        if (preg_match('/^(?:git@|https:\/\/)([^\/:]+)[\/:](.+)\.git$/', $gitUrl, $matches)) {
            return 'https://' . $matches[1] . '/' . $matches[2];
        }
    }
}
