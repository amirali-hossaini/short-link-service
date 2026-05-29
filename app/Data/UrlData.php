<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class UrlData extends Data
{
    public function __construct(
        public int $userId,
        public string $originUrl,
        public string $shortCode,
        public int $viewsCount = 0,
        public ?string $expiresAt = null,
    ) {}
}
