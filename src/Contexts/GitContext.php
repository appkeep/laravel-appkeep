<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class GitContext implements Arrayable
{
    public function toArray()
    {
        return [
            'head' => $this->shortCommitHash(),
            'origin' => $this->repositoryUrl(),
        ];
    }

    private function commitHash()
    {
        $output = shell_exec('cd ' . base_path() . ' && git rev-parse HEAD 2> /dev/null');

        if (!$output) {
            return null;
        }

        return $output;
    }

    private function shortCommitHash()
    {
        if ($commitHash = $this->commitHash()) {
            return substr($commitHash, 0, 7);
        }
    }

    private function repositoryUrl()
    {
        $gitUrl = shell_exec('cd ' . base_path() . ' && git config --get remote.origin.url 2> /dev/null');

        // match repository name from remote origin url
        if (preg_match('/^(?:git@|https:\/\/)([^\/:]+)[\/:](.+)\.git$/', $gitUrl, $matches)) {
            return 'https://' . $matches[1] . '/' . $matches[2];
        }
    }
}
