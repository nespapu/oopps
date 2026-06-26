<?php

declare(strict_types=1);

namespace App\Application\Exercises\HowMuchDoYouKnow\Shared;

final class DiceCoefficientSimilarityEvaluator implements SimilarityEvaluator
{
    public function compare(string $expected, string $actual): float
    {
        $expected = $this->removeWhitespace($expected);
        $actual = $this->removeWhitespace($actual);

        if ($expected === $actual) {
            return 1.0;
        }

        $expectedBigrams = $this->getBigrams($expected);
        $actualBigrams = $this->getBigrams($actual);

        $expectedBigramsCount = count($expectedBigrams);
        $actualBigramsCount = count($actualBigrams);

        if ($expectedBigramsCount === 0 || $actualBigramsCount === 0) {
            return 0.0;
        }

        $matches = $this->countMatchingBigrams($expectedBigrams, $actualBigrams);

        return (2 * $matches) / ($expectedBigramsCount + $actualBigramsCount);
    }

    private function getBigrams(string $text): array
    {
        $bigrams = [];
        $length = strlen($text);

        for ($i = 0; $i < $length - 1; $i++) {
            $bigrams[] = substr($text, $i, 2);
        }

        return $bigrams;
    }

    private function removeWhitespace(string $text): string
    {
        return preg_replace('/\s+/', '', $text) ?? '';
    }

    private function countMatchingBigrams(array $expectedBigrams, array $actualBigrams): int
    {
        $matches = 0;

        foreach ($actualBigrams as $actualBigram) {
            $index = array_search($actualBigram, $expectedBigrams, true);

            if ($index !== false) {
                $matches++;
                unset($expectedBigrams[$index]);
            }
        }
        
        return $matches;
    }
}