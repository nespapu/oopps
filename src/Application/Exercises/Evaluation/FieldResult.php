<?php

declare(strict_types=1);

namespace App\Application\Exercises\Evaluation;

final class FieldResult
{
    public function __construct(
        public readonly string $fieldKey,
        public readonly string $actual,
        public readonly bool $isCorrect,
        public readonly string $strategy,            // "equality" | "similarity"
        public readonly ?float $similarityScore = null,
        public readonly ?string $message = null
    ) {}
}
