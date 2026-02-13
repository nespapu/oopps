<?php

declare(strict_types=1);

namespace App\Application\Exercises\Evaluation;

final class StepResult
{
    /**
     * @param array<string, FieldResult> $fieldResults
     */
    public function __construct(
        public readonly string $step,               // e.g. "title"
        public readonly array $fieldResults,
        public readonly bool $isStepCorrect,
        public readonly ?float $score = null         // optional
    ) {}
}
