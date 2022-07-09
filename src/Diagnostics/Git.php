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

    public static function remoteUrl()
    {
        $gitUrl = shell_exec('cd ' . base_path() . ' && git config --get remote.origin.url 2> /dev/null');

        if (! $gitUrl) {
            return null;
        }

        $vcsUrl = trim($gitUrl);
        $vcsUrl = str_replace(".git", "", $gitUrl);
        $vcsUrl = str_replace("git@", "", $gitUrl);

        if (strpos($gitUrl, 'https://') !== 0) {
            $vcsUrl = "https://" . $vcsUrl;
        }

        return $vcsUrl;
    }
}
