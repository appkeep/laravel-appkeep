<?php

namespace Appkeep\Eye\Checks;

use Appkeep\Eye\Result;

class HeartbeatCheck extends AbstractCheck
{
    /**
     * This check always passes by default.
     * It shows us that the checks are running on your server.
     */
    public function run()
    {
        return Result::ok();
    }
}
