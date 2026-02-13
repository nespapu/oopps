<?php

declare(strict_types=1);

namespace App\Application\Exercises\Evaluation;

final class TextNormalizer
{
    public function normalize(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return mb_strtolower($value);
    }
}
