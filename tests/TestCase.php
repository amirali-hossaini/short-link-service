<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected User $user;

    protected $seed = true;

    protected $seeder = TestDatabaseSeeder::class;

    public function apiUri(string $uri = ''): string
    {
        return 'api/'.$uri;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->setHeaders();
    }

    protected function getCollectionMainResponseKeys(): array
    {
        return [
            'data' => [
                'items',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ],
            'message',
            'errors',
        ];
    }

    private function setHeaders(): void
    {
        $this->withHeaders([
            'Accept' => 'application/json',
        ]);
    }

    private function authenticate(): void
    {
        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }
}
