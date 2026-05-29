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

    public function findBy(string $column, mixed $value): ?User
    {
        return User::firstWhere($column, $value);
    }

    public function existsBy(string $column, mixed $value): bool
    {
        return User::where($column, $value)->exists();
    }
}
