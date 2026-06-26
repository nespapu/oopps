<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Shared;

use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use PHPUnit\Framework\TestCase;

final class DiceCoefficientSimilarityEvaluatorTest extends TestCase
{
    public function testSameTextsReturnOne(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'Introducción', 
            'Introducción'
        );

        $this->assertSame(1.0, $score);
    }

    public function testCompletelyDifferentTextsReturnZero(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'abc', 
            'xyz'
        );

        $this->assertSame(0.0, $score);
    }

    public function testSimilarTextsReturnPartialScore(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'night', 
            'nacht'
        );

        $this->assertEqualsWithDelta(0.25, $score, 0.0001);
    }

    public function testIgnoresWhitespace(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'base de datos', 
            'basededatos'
        );

        $this->assertSame(1.0, $score);
    }

    public function testDifferentSingleCharacterTextsReturnZero(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'a', 
            'b'
        );

        $this->assertSame(0.0, $score);
    }
    
    public function testEmptyAndNonEmptyTextsReturnZero(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            '', 
            'a'
        );

        $this->assertSame(0.0, $score);
    }

    public function testSimilarSentenceReturnsHighScore(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();

        $score = $evaluator->compare(
            'Programación orientada a objetos',
            'Programacion orientada objetos'
        );

        $this->assertGreaterThan(0.75, $score);
    }

    public function testRepeatedBigramsAreMatchedOnlyOnce(): void
    {
        $evaluator = new DiceCoefficientSimilarityEvaluator();
    
        $score = $evaluator->compare('aaaa', 'aaab');
    
        $this->assertEqualsWithDelta(2.0 / 3.0, $score, 0.0001);
    }

}