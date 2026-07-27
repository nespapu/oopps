<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Quotes\QuotesEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class QuotesEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::QUOTES->value,
            'values' => [
                'quote0.quoteConcept' => 'Concept A',
                'quote0.quoteAuthor' => 'Author A',
                'quote0.quoteYear' => 'Year 1',
                'quote0.quoteContent' => 'Content A',
                'quote0.quoteSectionOrder' => '1',
                'quote0.quoteSectionTitle' => 'Title 1',
                'quote1.quoteConcept' => 'Concept B',
                'quote1.quoteAuthor' => 'Author B',
                'quote1.quoteYear' => 'Year 2',
                'quote1.quoteContent' => 'Content B',
                'quote1.quoteSectionOrder' => '2',
                'quote1.quoteSectionTitle' => 'Title 2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionTitle']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::QUOTES->value,
            'values' => [
                'quote0.quoteConcept' => 'Concept C',
                'quote0.quoteAuthor' => 'Author A',
                'quote0.quoteYear' => 'Year 1',
                'quote0.quoteContent' => 'Content A',
                'quote0.quoteSectionOrder' => '1',
                'quote0.quoteSectionTitle' => 'Title 1',
                'quote1.quoteConcept' => 'Concept B',
                'quote1.quoteAuthor' => 'Author B',
                'quote1.quoteYear' => 'Year 2',
                'quote1.quoteContent' => 'Content B',
                'quote1.quoteSectionOrder' => '2',
                'quote1.quoteSectionTitle' => 'Title 2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['quote0.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionTitle']->isCorrect);        
    }

    public function testReturnsIncorrectWhenSimilarityScoreIsBelowThreshold(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );


        $stepAnswer = [
            'step' => ExerciseStep::QUOTES->value,
            'values' => [
                'quote0.quoteConcept' => 'Concept A',
                'quote0.quoteAuthor' => 'Author A',
                'quote0.quoteYear' => 'Year 1',
                'quote0.quoteContent' => 'Carant A',
                'quote0.quoteSectionOrder' => '1',
                'quote0.quoteSectionTitle' => 'Title 1',
                'quote1.quoteConcept' => 'Concept B',
                'quote1.quoteAuthor' => 'Author B',
                'quote1.quoteYear' => 'Year 2',
                'quote1.quoteContent' => 'Content B',
                'quote1.quoteSectionOrder' => '2',
                'quote1.quoteSectionTitle' => 'Title 2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);

        $fieldResult = $evaluation->result->fieldResults['quote0.quoteContent'];
        $this->assertNotNull($fieldResult->similarityScore);
        $this->assertLessThan(0.8, $fieldResult->similarityScore);
        $this->assertFalse($fieldResult->isCorrect);
        
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionTitle']->isCorrect);        
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(false, true, false, true, false, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::QUOTES->value,
            'values' => [
                'quote0.quoteAuthor' => 'Author A',
                'quote0.quoteContent' => 'Content A',
                'quote0.quoteSectionTitle' => 'Title 1',
                'quote1.quoteAuthor' => 'Author B',
                'quote1.quoteContent' => 'Content B',
                'quote1.quoteSectionTitle' => 'Title 2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        
        $this->assertArrayNotHasKey('quote0.quoteConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('quote1.quoteConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('quote0.quoteYear', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('quote1.quoteYear', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('quote0.quoteSectionOrder', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('quote1.quoteSectionOrder', $evaluation->result->fieldResults);
        
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote0.quoteSectionTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteContent']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['quote1.quoteSectionTitle']->isCorrect);
    }
    
    private function createService(): QuotesEvaluationService
    {
        return new QuotesEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            ),
            new DiceCoefficientSimilarityEvaluator()
        );
    }
    
    private function createPayload(array $fieldConfig): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::QUOTES->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'quote0',
                    'quoteConcept' => [
                        'value' => 'Concept A'
                    ],
                    'quoteAuthor' => [
                        'value' => 'Author A'
                    ],
                    'quoteYear' => [
                        'value' => 'Year 1'
                    ],
                    'quoteContent' => [
                        'value' => 'Content A'
                    ],
                    'quoteSectionOrder' => [
                        'value' => '1'
                    ],
                    'quoteSectionTitle' => [
                        'value' => 'Title 1'
                    ],                    
                ],
                [
                    'key' => 'quote1',
                    'quoteConcept' => [
                        'value' => 'Concept B'
                    ],
                    'quoteAuthor' => [
                        'value' => 'Author B'
                    ],
                    'quoteYear' => [
                        'value' => 'Year 2'
                    ],
                    'quoteContent' => [
                        'value' => 'Content B'
                    ],
                    'quoteSectionOrder' => [
                        'value' => '2'
                    ],
                    'quoteSectionTitle' => [
                        'value' => 'Title 2'
                    ],                    
                ]
            ],
            StepPayloadKeys::EXPECTED => [
                [
                    'key' => 'quote0',
                    'quoteConcept' => 'Concept A',
                    'quoteAuthor' => 'Author A',
                    'quoteYear' => 'Year 1',
                    'quoteContent' => 'Content A',
                    'quoteSectionOrder' => '1',
                    'quoteSectionTitle' => 'Title 1',                    
                ],
                [
                    'key' => 'quote1',
                    'quoteConcept' => 'Concept B',
                    'quoteAuthor' => 'Author B',
                    'quoteYear' => 'Year 2',
                    'quoteContent' => 'Content B',
                    'quoteSectionOrder' => '2',
                    'quoteSectionTitle' => 'Title 2',                    
                ]
            ]
        ];
    }

    private function createFieldConfig(
        bool $evalQuoteConcept,
        bool $evalQuoteAuthor,
        bool $evalQuoteYear,
        bool $evalQuoteContent,
        bool $evalQuoteSectionOrder,
        bool $evalQuoteSectionTitle
    ): array
    {
        return  
            [
                'quoteConcept' => [
                    'evaluable' => $evalQuoteConcept,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'quoteAuthor' => [
                    'evaluable' => $evalQuoteAuthor,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'quoteYear' => [
                    'evaluable' => $evalQuoteYear,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'quoteContent' => [
                    'evaluable' => $evalQuoteContent,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],
                'quoteSectionOrder' => [
                    'evaluable' => $evalQuoteSectionOrder,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'quoteSectionTitle' => [
                    'evaluable' => $evalQuoteSectionTitle,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],                
            ];
    }
}