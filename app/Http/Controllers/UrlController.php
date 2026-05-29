<?php

namespace App\Http\Controllers;

use App\Data\UrlData;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Requests\UpdateUrlRequest;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Repositories\Contracts\UrlRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function __construct(
        private readonly UrlRepositoryInterface $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $urls = $this->repository->paginateByUser(auth()->id());

        return $this
            ->resource(UrlResource::class)
            ->data($urls)
            ->successResponse();
    }

    public function store(StoreUrlRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['user_id'] = auth()->id();

        $existingUrl = $this->repository->findByUserAndOriginUrl(
            auth()->id(),
            $payload['origin_url'],
        );

        if ($existingUrl) {
            return $this
                ->resource(UrlResource::class)
                ->data($existingUrl)
                ->message('Short URL already exists.')
                ->successResponse();
        }

        if (isset($payload['alias'])) {
            $shortCode = $payload['alias'];

            unset($payload['alias']);
        } else {
            do {
                $shortCode = Str::random(8);
            } while ($this->repository->existsBy('short_code', $shortCode));
        }

        $payload['short_code'] = $shortCode;

        $url = $this->repository->create(UrlData::from($payload));

        return $this
            ->resource(UrlResource::class)
            ->data($url)
            ->message('Short URL created successfully.')
            ->createdResponse();
    }

    public function update(UpdateUrlRequest $request, Url $url): JsonResponse
    {
        if ($url->user_id !== auth()->id()) {
            return $this->notFoundResponse();
        }

        $payload = $request->validated();

        if (isset($payload['origin_url'])) {
            $existingUrl = $this->repository->findByUserAndOriginUrl(
                auth()->id(),
                $payload['origin_url'],
            );

            if ($existingUrl && $existingUrl->id !== $url->id) {
                return $this
                    ->message('A short URL already exists for this URL.')
                    ->validationErrorResponse([]);
            }
        }

        if (isset($payload['alias'])) {
            $payload['short_code'] = $payload['alias'];

            unset($payload['alias']);
        }

        $this->repository->update($url, $payload);

        $url->refresh();

        return $this
            ->resource(UrlResource::class)
            ->data($url)
            ->message('Short URL updated successfully.')
            ->successResponse();
    }

    public function destroy(Url $url): JsonResponse
    {
        if ($url->user_id !== auth()->id()) {
            return $this->notFoundResponse();
        }

        $url->delete();

        return $this
            ->message('Short URL deleted successfully.')
            ->successResponse();
    }
}
