<?php

declare(strict_types=1);

namespace App\Application\Exercises\HowMuchDoYouKnow\Shared;

final class EqualityEvaluator
{
    public function __construct(
        private readonly TextNormalizer $normalizer
    ) {}

    public function evaluate(string $actual, string $expected): bool
    {
        $normalizedActual = $this->normalizer->normalize($actual);
        $normalizedExpected = $this->normalizer->normalize($expected);

        return $normalizedActual === $normalizedExpected;
    }
}
