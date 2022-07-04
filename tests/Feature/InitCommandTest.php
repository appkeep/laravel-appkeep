<?php

namespace Tests\Feature;

use Tests\TestCase;

class InitCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_publishes_the_config_file()
    {
        @unlink(config_path('appkeep.php'));

        $this->artisan('appkeep:init');

        $this->assertFileExists(config_path('appkeep.php'));
    }

    /**
     * @test
     */
    public function it_publishes_the_health_checks_provider()
    {
        @unlink(config_path('appkeep.php'));

        $this->artisan('appkeep:init');

        $this->assertFileExists(config_path('appkeep.php'));
    }
}
