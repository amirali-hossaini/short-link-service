<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

trait ApiResponseTrait
{
    private ?string $resource = null;

    private ?string $message = null;

    private mixed $data = null;

    /**
     * Set the resource class.
     *
     * @param  class-string<JsonResource>  $resource
     */
    protected function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    protected function message(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set response data.
     */
    protected function data(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Return a standardized JSON response.
     */
    protected function apiResponse(
        array $errors = [],
        int $statusCode = 200,
    ): JsonResponse {
        $message = $this->message ?? '';
        $resource = $this->resource;
        $data = $this->data;

        $this->message = null;
        $this->resource = null;
        $this->data = null;

        if ($resource && ! ($data instanceof JsonResource) && ! ($data instanceof ResourceCollection)) {
            if ($data instanceof LengthAwarePaginator) {
                $items = $resource::collection($data->items());

                $responseData = [
                    'items' => $items,
                    'meta' => $this->paginatorMeta($data),
                    'links' => $this->paginatorLinks($data),
                ];
            } elseif ($data instanceof Collection || is_array($data)) {
                $responseData = [
                    'items' => $resource::collection($data),
                ];
            } else {
                $responseData = (new $resource($data))->resolve(request());
            }
        } else {
            $responseData = $data;
        }

        return response()->json([
            'data' => $responseData,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Return a success response.
     */
    protected function successResponse(
        int $statusCode = Response::HTTP_OK,
    ): JsonResponse {
        return $this->apiResponse([], $statusCode);
    }

    /**
     * Return a created response.
     */
    protected function createdResponse(
        int $statusCode = Response::HTTP_CREATED,
    ): JsonResponse {
        return $this->apiResponse([], $statusCode);
    }

    /**
     * Return an error response.
     */
    protected function errorResponse(
        array $errors = [],
        int $statusCode = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        return $this->apiResponse($errors, $statusCode);
    }

    /**
     * Return a validation error response.
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this
            ->message($this->message ?? __('validation.failed'))
            ->apiResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return a not found response.
     */
    protected function notFoundResponse(): JsonResponse
    {
        return $this
            ->message($this->message ?? __('common.not_found'))
            ->apiResponse([], Response::HTTP_NOT_FOUND);
    }

    /**
     * Return an unauthorized response.
     */
    protected function unauthorizedResponse(): JsonResponse
    {
        return $this
            ->message($this->message ?? __('common.unauthorized'))
            ->apiResponse([], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a forbidden response.
     */
    protected function forbiddenResponse(): JsonResponse
    {
        return $this
            ->message($this->message ?? __('common.forbidden'))
            ->apiResponse([], Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a server error response.
     */
    protected function serverErrorResponse(array $errors = []): JsonResponse
    {
        return $this
            ->message($this->message ?? __('common.server_error'))
            ->apiResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Build meta array for a length-aware paginator.
     */
    private function paginatorMeta(LengthAwarePaginator $data): array
    {
        return [
            'current_page' => $data->currentPage(),
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'to' => $data->lastItem(),
            'total' => $data->total(),
        ];
    }

    /**
     * Build links array for a length-aware paginator.
     */
    private function paginatorLinks(LengthAwarePaginator $data): array
    {
        return [
            'first' => $data->url(1),
            'last' => $data->url($data->lastPage()),
            'prev' => $data->previousPageUrl(),
            'next' => $data->nextPageUrl(),
        ];
    }
}
