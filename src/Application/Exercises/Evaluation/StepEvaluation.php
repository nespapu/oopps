<?php

declare(strict_types=1);

namespace App\Application\Exercises\Evaluation;

final class StepEvaluation
{
    public function __construct(
        public readonly StepResult $result,
        public readonly \DateTimeImmutable $createdAt
    ) {}

    public static function now(StepResult $result): self
    {
        return new self($result, new \DateTimeImmutable());
    }
}
