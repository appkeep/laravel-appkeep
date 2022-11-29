<?php

namespace Tests\Feature;

use Tests\TestCase;

class InsightsTest extends TestCase
{
    /**
     * @test
     */
    public function insights_return_500_if_no_key_is_set()
    {
        $this->get(route('appkeep.insights'))->assertStatus(500);
    }

    /**
     * @test
     */
    public function insights_return_401_if_key_hash_is_incorrect()
    {
        config()->set('appkeep.key', 'xyz');
        $this->get(route('appkeep.insights'), ['x-appkeep-key' => 'random'])->assertStatus(401);
    }

    /**
     * @test
     */
    public function it_returns_insights()
    {
        config()->set('appkeep.key', 'xyz');

        $this->get(
            route('appkeep.insights'),
            ['x-appkeep-key' => hash('sha256', 'xyz')]
        )->assertOk();
    }
}
