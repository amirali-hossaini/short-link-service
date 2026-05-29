<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private string $baseUri = 'auth';

    public function test_user_can_register(): void
    {
        $response = $this->postJson($this->apiUri("{$this->baseUri}/register"), [
            'name' => 'John Doe',
            'email' => 'john-doe@gmail.com',
            'password' => 'password',
        ]);

        $response->assertCreated();
        $response->assertJson([
            'message' => 'Registered successfully. Please log in.',
        ]);

        $this->assertDatabaseHas(User::class, [
            'name' => 'John Doe',
            'email' => 'john-doe@gmail.com',
        ]);
    }

    public function test_register_does_not_create_duplicate_user(): void
    {
        User::factory()->create([
            'email' => 'john-doe@gmail.com',
        ]);

        $response = $this->postJson($this->apiUri("{$this->baseUri}/register"), [
            'name' => 'John Doe',
            'email' => 'john-doe@gmail.com',
            'password' => 'password',
        ]);

        $response->assertCreated();

        $this->assertDatabaseCount('users', 2); // includes the authenticated test user from TestCase
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'john-doe@gmail.com',
        ]);

        $response = $this->postJson($this->apiUri("{$this->baseUri}/login"), [
            'email' => 'john-doe@gmail.com',
            'password' => 'password',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                ],
                'token',
                'token_type',
            ],
            'message',
            'errors',
        ]);

        $response->assertJson([
            'message' => 'Logged in successfully.',
            'data' => [
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john-doe@gmail.com',
        ]);

        $response = $this->postJson($this->apiUri("{$this->baseUri}/login"), [
            'email' => 'john-doe@gmail.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();

        $response->assertJson([
            'message' => 'Invalid credentials.',
        ]);
    }
}
