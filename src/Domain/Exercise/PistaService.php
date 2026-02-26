<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class HintService
{
    private const CHARSET = 'UTF-8';
    private const WORD_MASK = '____';
    private const LETTER_MASK = '_';

    /**
     * Domain rule: Linking words (articles, prepositions, conjunctions)
     * are never masked and are not considered semantically meaningful content words.
     */
    private const STOP_WORDS = [
        'a', 'al', 'con', 'de', 'del', 'el', 'en',
        'la', 'las', 'los', 'para', 'por', 'sin', 'y',
    ];

    public function getHint(
        string $value,
        Difficulty $difficulty,
        HintMode $mode
    ): string {
        if (trim($value) === '') {
            return '';
        }

        $value = $this->normalizeSpaces($value);

        $words = explode(' ', $value);
        $contentWordIndexes = $this->getSemanticContentWordIndexes($words);

        return match ($mode) {
            HintMode::WORDS   => $this->generateWordHint($words, $contentWordIndexes, $difficulty, $value),
            HintMode::LETTERS => $this->generateLetterHint($words, $contentWordIndexes, $difficulty),
        };
    }

    private function normalizeSpaces(string $value): string
    {
        $value = trim($value);
        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function generateWordHint(
        array $words,
        array $contentWordIndexes,
        Difficulty $difficulty,
        string $originalValue
    ): string {
        $total = count($contentWordIndexes);

        return match ($difficulty) {
            Difficulty::VERY_EASY => $this->veryEasyWords($words, $contentWordIndexes, $total, $originalValue),
            Difficulty::EASY      => $this->easyWords($words, $contentWordIndexes, $total, $originalValue),
            Difficulty::MEDIUM    => $this->mediumWords($words, $contentWordIndexes, $total, $originalValue),
            Difficulty::HARD      => $this->hardWords($words, $contentWordIndexes, $total, $originalValue),
        };
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function veryEasyWords(array $words, array $contentWordIndexes, int $total, string $originalValue): string
    {
        if ($total === 0) {
            return $originalValue;
        }

        $middlePosition = intdiv($total, 2);
        $index = $contentWordIndexes[$middlePosition];

        $words[$index] = $this->maskWordCoreForWords($words[$index]);

        return implode(' ', $words);
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function easyWords(array $words, array $contentWordIndexes, int $total, string $originalValue): string
    {
        if ($total < 2) {
            return $originalValue;
        }

        $leftPosition = intdiv($total - 1, 2);
        $rightPosition = $leftPosition + 1;

        $leftIndex = $contentWordIndexes[$leftPosition];
        $rightIndex = $contentWordIndexes[$rightPosition];

        $words[$leftIndex] = $this->maskWordCoreForWords($words[$leftIndex]);
        $words[$rightIndex] = $this->maskWordCoreForWords($words[$rightIndex]);

        return implode(' ', $words);
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function mediumWords(array $words, array $contentWordIndexes, int $total, string $originalValue): string
    {
        if ($total <= 2) {
            return $originalValue;
        }

        $firstIndex = $contentWordIndexes[0];
        $lastIndex  = $contentWordIndexes[$total - 1];

        foreach ($contentWordIndexes as $index) {
            if ($index === $firstIndex || $index === $lastIndex) {
                continue;
            }
            $words[$index] = $this->maskWordCoreForWords($words[$index]);
        }

        return implode(' ', $words);
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function hardWords(array $words, array $contentWordIndexes, int $total, string $originalValue): string
    {
        if ($total <= 1) {
            return $originalValue;
        }

        $firstIndex = $contentWordIndexes[0];

        foreach ($contentWordIndexes as $index) {
            if ($index === $firstIndex) {
                continue;
            }
            $words[$index] = $this->maskWordCoreForWords($words[$index]);
        }

        return implode(' ', $words);
    }

    /**
     * @param string[] $words
     * @param int[] $contentWordIndexes
     */
    private function generateLetterHint(array $words, array $contentWordIndexes, Difficulty $difficulty): string
    {
        $masker = match ($difficulty) {
            Difficulty::VERY_EASY => fn(string $w): string => $this->maskLastLetter($w),
            Difficulty::EASY      => fn(string $w): string => $this->maskSecondHalfLetters($w),
            Difficulty::MEDIUM    => fn(string $w): string => $this->maskFirstAndLastLetter($w),
            Difficulty::HARD      => fn(string $w): string => $this->maskAllLettersExceptFirst($w),
        };

        foreach ($contentWordIndexes as $index) {
            $words[$index] = $this->maskWordCoreForLetters($words[$index], $masker);
        }

        return implode(' ', $words);
    }

    /**
     * Returns the indexes of semantically meaningful content words,
     * excluding linking words from being masked or counted.
     *
     * @param string[] $words
     * @return int[]
     */
    private function getSemanticContentWordIndexes(array $words): array
    {
        $indexes = [];

        foreach ($words as $i => $word) {
            if ($word === '') {
                continue;
            }

            if ($this->isStopWord($word)) {
                continue;
            }

            $indexes[] = $i;
        }

        return $indexes;
    }

    private function isStopWord(string $word): bool
    {
        $normalized = $this->normalizeWordForComparison($word);

        if ($normalized === '') {
            return false;
        }

        return in_array($normalized, self::STOP_WORDS, true);
    }

    /**
     * Normalizes a word for comparison against stop words:
     * - Lowercases it
     * - Removes leading/trailing punctuation, keeps inner punctuation
     * - Preserves accents
     */
    private function normalizeWordForComparison(string $word): string
    {
        $word = mb_strtolower($word, self::CHARSET);
        $word = preg_replace('/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/u', '', $word);

        return $word ?? '';
    }

    private function maskLastLetter(string $word): string
    {
        $length = mb_strlen($word, self::CHARSET);

        if ($length <= 1) {
            return $word;
        }

        return mb_substr($word, 0, $length - 1, self::CHARSET) . self::LETTER_MASK;
    }

    private function maskSecondHalfLetters(string $word): string
    {
        $length = mb_strlen($word, self::CHARSET);

        if ($length <= 1) {
            return $word;
        }

        $visibleCount = intdiv($length + 1, 2);
        $maskedCount = $length - $visibleCount;

        return mb_substr($word, 0, $visibleCount, self::CHARSET)
            . str_repeat(self::LETTER_MASK, $maskedCount);
    }

    private function maskFirstAndLastLetter(string $word): string
    {
        $length = mb_strlen($word, self::CHARSET);

        if ($length <= 2) {
            return $word;
        }

        $first = mb_substr($word, 0, 1, self::CHARSET);
        $last  = mb_substr($word, $length - 1, 1, self::CHARSET);

        return $first . str_repeat(self::LETTER_MASK, $length - 2) . $last;
    }

    private function maskAllLettersExceptFirst(string $word): string
    {
        $length = mb_strlen($word, self::CHARSET);

        if ($length <= 1) {
            return $word;
        }

        $first = mb_substr($word, 0, 1, self::CHARSET);

        return $first . str_repeat(self::LETTER_MASK, $length - 1);
    }

    private function maskWordCoreForLetters(string $word, callable $masker): string
    {
        [$prefix, $core, $suffix] = $this->splitWord($word);

        $maskedCore = $masker($core);

        return $prefix . $maskedCore . $suffix;
    }

    private function maskWordCoreForWords(string $word): string
    {
        [$prefix, $core, $suffix] = $this->splitWord($word);

        // Mask only if there is a meaningful core
        if ($core === '' || !preg_match('/[\p{L}\p{N}]/u', $core)) {
            return $word;
        }

        return $prefix . self::WORD_MASK . $suffix;
    }

    /**
     * @return array{0:string,1:string,2:string}
     */
    private function splitWord(string $word): array
    {
        // prefix: non-alphanumeric characters at the start
        // core:    alphanumeric characters
        // suffix: non-alphanumeric characters at the end
        if (preg_match('/^([^\p{L}\p{N}]*)([\p{L}\p{N}]+)([^\p{L}\p{N}]*)$/u', $word, $m) !== 1) {
            return ['', $word, ''];
        }

        return [$m[1], $m[2], $m[3]];
    }
}