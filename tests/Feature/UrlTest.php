<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    private string $baseUri = 'urls';

    public function test_user_can_get_paginated_urls(): void
    {
        Url::factory()
            ->count(5)
            ->user($this->user->id)
            ->create();

        Url::factory()->count(5)->create();

        $response = $this->getJson($this->apiUri($this->baseUri));

        $response->assertOk();

        $response->assertJsonStructure(
            $this->getCollectionMainResponseKeys()
        );

        $response->assertJsonStructure([
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'origin_url',
                        'short_code',
                        'short_url',
                        'views_count',
                        'expires_at',
                    ],
                ],
            ],
        ]);

        $this->assertCount(5, $response->json('data.items'));
    }

    public function test_user_can_create_short_url(): void
    {
        $response = $this->postJson(
            $this->apiUri($this->baseUri),
            [
                'origin_url' => 'https://google.com',
                'expires_at' => now()->addMonth()->toDateString(),
            ],
        );

        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'origin_url',
                'short_code',
                'short_url',
                'views_count',
                'expires_at',
            ],
        ]);

        $response->assertJson([
            'message' => 'Short URL created successfully.',
        ]);

        $this->assertDatabaseHas(Url::class, [
            'id' => $response->json('data.id'),
            'user_id' => $this->user->id,
            'origin_url' => 'https://google.com',
            'expires_at' => now()->startOfDay()->addMonth()->toDateTimeString(),
        ]);
    }

    public function test_user_can_visit_short_url(): void
    {
        $url = Url::factory()->create();

        $response = $this->getJson(
            $this->apiUri("{$this->baseUri}/{$url->short_code}/visit")
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'destination_url',
            ],
        ]);

        $this->assertDatabaseHas(Url::class, [
            'id' => $url->id,
            'views_count' => 1,
        ]);
    }

    public function test_user_cannot_visit_expired_short_url(): void
    {
        $url = Url::factory()
            ->expired()
            ->create();

        $response = $this->getJson(
            $this->apiUri("{$this->baseUri}/{$url->short_code}/visit")
        );

        $response->assertNotFound();

        $response->assertJson([
            'message' => 'Short URL is no longer valid.',
        ]);
    }

    public function test_user_can_create_short_url_with_alias(): void
    {
        $response = $this->postJson(
            $this->apiUri($this->baseUri),
            [
                'origin_url' => 'https://google.com',
                'alias' => 'google',
            ],
        );

        $response->assertCreated();

        $response->assertJsonPath('data.short_code', 'google');

        $this->assertDatabaseHas(Url::class, [
            'user_id' => $this->user->id,
            'origin_url' => 'https://google.com',
            'short_code' => 'google',
        ]);
    }

    public function test_user_can_update_short_url_alias(): void
    {
        $url = Url::factory()
            ->user($this->user->id)
            ->create([
                'short_code' => 'old-alias',
            ]);

        $response = $this->putJson(
            $this->apiUri("{$this->baseUri}/{$url->id}"),
            [
                'alias' => 'new-alias',
            ],
        );

        $response->assertOk();

        $response->assertJsonPath('data.short_code', 'new-alias');

        $this->assertDatabaseHas(Url::class, [
            'id' => $url->id,
            'short_code' => 'new-alias',
        ]);
    }

    public function test_users_cannot_use_same_alias(): void
    {
        Url::factory()->create([
            'short_code' => 'google',
        ]);

        $response = $this->postJson(
            $this->apiUri($this->baseUri),
            [
                'origin_url' => 'https://google.com',
                'alias' => 'google',
            ],
        );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'alias',
        ]);
    }

    public function test_user_can_update_short_url(): void
    {
        $url = Url::factory()->user($this->user->id)->create();

        $response = $this->putJson(
            $this->apiUri("{$this->baseUri}/{$url->id}"),
            [
                'origin_url' => 'https://github.com',
                'expires_at' => now()->addYear()->toDateString(),
            ],
        );

        $response->assertOk();

        $response->assertJson([
            'message' => 'Short URL updated successfully.',
        ]);

        $this->assertDatabaseHas(Url::class, [
            'id' => $url->id,
            'origin_url' => 'https://github.com',
            'expires_at' => now()->startOfDay()->addYear()->toDateTimeString(),
        ]);
    }

    public function test_user_can_delete_short_url(): void
    {
        $url = Url::factory()->user($this->user->id)->create();

        $response = $this->deleteJson(
            $this->apiUri("{$this->baseUri}/{$url->id}")
        );

        $response->assertOk();

        $response->assertJson([
            'message' => 'Short URL deleted successfully.',
        ]);

        $this->assertDatabaseMissing(Url::class, [
            'id' => $url->id,
        ]);
    }

    public function test_user_cannot_update_another_users_short_url(): void
    {
        $url = Url::factory()->create();

        $response = $this->putJson(
            $this->apiUri("{$this->baseUri}/{$url->id}"),
            [
                'origin_url' => 'https://github.com',
            ],
        );

        $response->assertNotFound();
    }

    public function test_user_cannot_delete_another_users_short_url(): void
    {
        $url = Url::factory()->create();

        $response = $this->deleteJson(
            $this->apiUri("{$this->baseUri}/{$url->id}")
        );

        $response->assertNotFound();
    }
}
