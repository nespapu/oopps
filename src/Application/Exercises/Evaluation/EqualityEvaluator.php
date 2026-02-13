<?php

declare(strict_types=1);

namespace App\Application\Exercises\Evaluation;

final class EqualityEvaluator
{
    public function __construct(
        private readonly TextNormalizer $normalizer
    ) {}

    public function evaluate(string $fieldkey, string $actual, string $expected): FieldResult
    {
        $normalizedActual = $this->normalizer->normalize($actual);
        $normalizedExpected = $this->normalizer->normalize($expected);

        $isCorrect = ($normalizedActual === $normalizedExpected);

        return new FieldResult(
            fieldKey: $fieldkey,
            actual: $actual,
            isCorrect: $isCorrect,
            strategy: 'equality',
            similarityScore: null,
            message: $isCorrect ? null : 'Answer does not match the expected value.'
        );
    }
}
