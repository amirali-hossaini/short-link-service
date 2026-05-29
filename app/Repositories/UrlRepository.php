<?php

namespace App\Repositories;

use App\Data\UrlData;
use App\Models\Url;
use App\Repositories\Contracts\UrlRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UrlRepository implements UrlRepositoryInterface
{
    public function paginateByUser(int $userId, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('app.per_page');

        return Url::query()->where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function create(UrlData $data): Url
    {
        return Url::create($data->toArray());
    }

    public function findBy(string $column, mixed $value): ?Url
    {
        return Url::firstWhere($column, $value);
    }

    public function findByUserAndOriginUrl(int $userId, string $originUrl): ?Url
    {
        return Url::query()
            ->where('user_id', $userId)
            ->where('origin_url', $originUrl)
            ->first();
    }

    public function existsBy(string $column, mixed $value): bool
    {
        return Url::where($column, $value)->exists();
    }
}
