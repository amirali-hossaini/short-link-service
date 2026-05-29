<?php

namespace App\Repositories;

use App\Data\UserData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(UserData $data): User
    {
        return User::create($data->toArray());
    }

    public function findBy(string $col, mixed $value): ?User
    {
        return User::firstWhere($col, $value);
    }

    public function existsBy(string $col, mixed $value): bool
    {
        return User::where($col, $value)->exists();
    }
}
