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

        $this->assertCount(5, $response->json('data.items'));
    }

    public function test_user_can_create_short_url(): void
    {
        $response = $this->postJson(
            $this->apiUri($this->baseUri),
            [
                'origin_url' => 'https://google.com',
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
            'message',
            'errors',
        ]);

        $response->assertJson([
            'message' => 'Short URL created successfully.',
        ]);

        $this->assertDatabaseHas(Url::class, [
            'id' => $response->json('data.id'),
            'user_id' => $this->user->id,
            'origin_url' => 'https://google.com',
        ]);
    }

    public function test_user_can_update_short_url(): void
    {
        $url = Url::factory()->user($this->user->id)->create();

        $response = $this->putJson(
            $this->apiUri("{$this->baseUri}/{$url->id}"),
            [
                'origin_url' => 'https://github.com',
            ],
        );

        $response->assertOk();

        $response->assertJson([
            'message' => 'Short URL updated successfully.',
        ]);

        $this->assertDatabaseHas(Url::class, [
            'id' => $url->id,
            'origin_url' => 'https://github.com',
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
