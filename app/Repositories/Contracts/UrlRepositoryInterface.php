<?php

namespace App\Repositories\Contracts;

use App\Data\UrlData;
use App\Models\Url;
use Illuminate\Pagination\LengthAwarePaginator;

interface UrlRepositoryInterface
{
    public function paginateByUser(int $userId, ?int $perPage = null): LengthAwarePaginator;

    public function create(UrlData $data): Url;

    public function findBy(string $column, mixed $value): ?Url;

    public function findByUserAndOriginUrl(int $userId, string $originUrl): ?Url;

    public function existsBy(string $column, mixed $value): bool;

    public function isValid(Url $url): bool;

    public function update(Url $url, array $data): bool;
}
