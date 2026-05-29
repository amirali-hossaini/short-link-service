<?php

namespace Database\Factories;

use App\Models\Url;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'origin_url' => fake()->url(),
            'short_code' => Str::random(8),
            'views_count' => 0,
            'expires_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function user(int $id): static
    {
        return $this->state(fn () => [
            'user_id' => $id,
        ]);
    }
}
