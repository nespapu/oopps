<?php

declare(strict_types=1);

namespace App\Application\Exercises\HowMuchDoYouKnow\Shared;

interface SimilarityEvaluator
{
    /**
     * Returns a similarity score between 0.0 and 1.0.
     */
    public function compare(string $expected, string $actual): float;
}