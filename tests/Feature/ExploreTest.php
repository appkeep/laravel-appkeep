<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ExploreTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    /**
     * @test
     */
    public function explore_returns_500_if_no_key_is_set()
    {
        $this->get(route('appkeep.explore'))->assertStatus(500);
    }

    /**
     * @test
     */
    public function explore_returns_401_if_key_hash_is_incorrect()
    {
        config()->set('appkeep.key', 'xyz');
        $this->get(route('appkeep.explore'), ['x-appkeep-key' => 'random'])->assertStatus(401);
    }

    /**
     * @test
     */
    public function it_returns_response()
    {
        config()->set('appkeep.key', 'xyz');

        $this->get(
            route('appkeep.explore'),
            ['x-appkeep-key' => hash('sha256', 'xyz')]
        )->assertOk();
    }
}
