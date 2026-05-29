<?php

namespace App\Repositories\Contracts;

use App\Data\UserData;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(UserData $data): User;

    public function findBy(string $column, mixed $value): ?User;

    public function existsBy(string $column, mixed $value): bool;
}
