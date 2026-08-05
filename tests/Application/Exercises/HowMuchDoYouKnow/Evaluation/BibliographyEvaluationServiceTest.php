<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Bibliography\BibliographyEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class BibliographyEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::BIBLIOGRAPHY->value,
            'values' => [
                'book0.bookAuthor' => 'Author A',
                'book0.bookPublicationYear' => 'Year A',
                'book0.bookTitle' => 'Title A',
                'book0.bookPublisher' => 'Publisher A',
                'book1.bookAuthor' => 'Author B',
                'book1.bookPublicationYear' => 'Year B',
                'book1.bookTitle' => 'Title B',
                'book1.bookPublisher' => 'Publisher B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublisher']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublisher']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::BIBLIOGRAPHY->value,
            'values' => [
                'book0.bookAuthor' => 'Author',
                'book0.bookPublicationYear' => 'Year A',
                'book0.bookTitle' => 'Title A',
                'book0.bookPublisher' => 'Publisher A',
                'book1.bookAuthor' => 'Author B',
                'book1.bookPublicationYear' => 'Year B',
                'book1.bookTitle' => 'Title B',
                'book1.bookPublisher' => 'Publisher B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['book0.bookAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublisher']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookAuthor']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookTitle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublisher']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(false, true, false, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::BIBLIOGRAPHY->value,
            'values' => [
                'book0.bookPublicationYear' => 'Year A',
                'book0.bookPublisher' => 'Publisher A',
                'book1.bookPublicationYear' => 'Year B',
                'book1.bookPublisher' => 'Publisher B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);
        
        $this->assertArrayNotHasKey('book0.bookAuthor', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('book1.bookAuthor', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('book0.bookTitle', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('book1.bookTitle', $evaluation->result->fieldResults);
        
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book0.bookPublisher']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublicationYear']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['book1.bookPublisher']->isCorrect);
    }

    private function createService(): BibliographyEvaluationService
    {
        return new BibliographyEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            )
        );
    }

    private function createPayload(
        array $fieldConfig,
        ?array $items = null,
        ?array $expected = null
    ): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::BIBLIOGRAPHY->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => $items ?? $this->defaultItems(),
            StepPayloadKeys::EXPECTED => $expected ?? $this->defaultExpected()
        ];
    }

    private function createFieldConfig(
        bool $evalBookAuthor,
        bool $evalBookPublicationYear,
        bool $evalBookTitle,
        bool $evalBookPublisher
    ): array
    {
        return  
            [
                'bookAuthor' => [
                    'evaluable' => $evalBookAuthor,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'bookPublicationYear' => [
                    'evaluable' => $evalBookPublicationYear,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'bookTitle' => [
                    'evaluable' => $evalBookTitle,
                    'evaluationMode' => EvaluationMode::EQUALITY,
                ],
                'bookPublisher' => [
                    'evaluable' => $evalBookPublisher,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                ],                
            ];
    }

    private function defaultItems(): array
    {
        return  [
            [
                'key' => 'book0',
                'bookAuthor' => [
                    'value' => 'Author A'
                ],
                'bookPublicationYear' => [
                    'value' => 'Year A'
                ],
                'bookTitle' => [
                    'value' => 'Title A'
                ],
                'bookPublisher' => [
                    'value' => 'Publisher A'
                ]
            ],
            [
                'key' => 'book1',
                'bookAuthor' => [
                    'value' => 'Author B'
                ],
                'bookPublicationYear' => [
                    'value' => 'Year B'
                ],
                'bookTitle' => [
                    'value' => 'Title B'
                ],
                'bookPublisher' => [
                    'value' => 'Publisher B'
                ]
            ],
        ];
    }

    private function defaultExpected(): array
    {
        return [
            [
                'key' => 'book0',
                'bookAuthor' => 'Author A',
                'bookPublicationYear' => 'Year A',
                'bookTitle' => 'Title A',
                'bookPublisher' => 'Publisher A',
            ],
            [
                'key' => 'book1',
                'bookAuthor' => 'Author B',
                'bookPublicationYear' => 'Year B',
                'bookTitle' => 'Title B',
                'bookPublisher' => 'Publisher B',
            ],                
        ];
    }
}