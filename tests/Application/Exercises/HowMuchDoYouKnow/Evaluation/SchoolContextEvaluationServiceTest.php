<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\SchoolContext\SchoolContextEvaluationService;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class SchoolContextEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::SCHOOL_CONTEXT->value,
            'values' => [
                'schoolContext0.schoolContextTeaching' => 'Teaching A',
                'schoolContext0.schoolContextCycle' => 'Cycle A',
                'schoolContext0.schoolContextModule' => 'Module A',
                'schoolContext0.schoolContextConcept' => 'Concept A',
                'schoolContext0.schoolContextApplication' => 'Application A',
                'schoolContext0.schoolContextMethod' => 'Method A',
                'schoolContext1.schoolContextTeaching' => 'Teaching B',
                'schoolContext1.schoolContextCycle' => 'Cycle B',
                'schoolContext1.schoolContextModule' => 'Module B',
                'schoolContext1.schoolContextConcept' => 'Concept B',
                'schoolContext1.schoolContextApplication' => 'Application B',
                'schoolContext1.schoolContextMethod' => 'Method B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextMethod']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextMethod']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::SCHOOL_CONTEXT->value,
            'values' => [
                'schoolContext0.schoolContextTeaching' => 'Teaching ',
                'schoolContext0.schoolContextCycle' => 'Cycle A',
                'schoolContext0.schoolContextModule' => 'Module A',
                'schoolContext0.schoolContextConcept' => 'Concept A',
                'schoolContext0.schoolContextApplication' => 'Application A',
                'schoolContext0.schoolContextMethod' => 'Method A',
                'schoolContext1.schoolContextTeaching' => 'Teaching B',
                'schoolContext1.schoolContextCycle' => 'Cycle B',
                'schoolContext1.schoolContextModule' => 'Module B',
                'schoolContext1.schoolContextConcept' => 'Concept B',
                'schoolContext1.schoolContextApplication' => 'Application B',
                'schoolContext1.schoolContextMethod' => 'Method B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['schoolContext0.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextMethod']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextMethod']->isCorrect);
    }

    public function testReturnsIncorrectWhenSimilarityScoreIsBelowThreshold(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::SCHOOL_CONTEXT->value,
            'values' => [
                'schoolContext0.schoolContextTeaching' => 'Teaching A',
                'schoolContext0.schoolContextCycle' => 'Cycle A',
                'schoolContext0.schoolContextModule' => 'Module A',
                'schoolContext0.schoolContextConcept' => 'Concept A',
                'schoolContext0.schoolContextApplication' => 'Appl',
                'schoolContext0.schoolContextMethod' => 'Method A',
                'schoolContext1.schoolContextTeaching' => 'Teaching B',
                'schoolContext1.schoolContextCycle' => 'Cycle B',
                'schoolContext1.schoolContextModule' => 'Module B',
                'schoolContext1.schoolContextConcept' => 'Concept B',
                'schoolContext1.schoolContextApplication' => 'Application B',
                'schoolContext1.schoolContextMethod' => 'Method B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        
        $fieldResult = $evaluation->result->fieldResults['schoolContext0.schoolContextApplication'];
        $this->assertNotNull($fieldResult->similarityScore);
        $this->assertLessThan(0.8, $fieldResult->similarityScore);
        $this->assertFalse($fieldResult->isCorrect);

        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextMethod']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextCycle']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextMethod']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, false, true, false, true, false)
        );

        $stepAnswer = [
            'step' => ExerciseStep::SCHOOL_CONTEXT->value,
            'values' => [
                'schoolContext0.schoolContextTeaching' => 'Teaching A',
                'schoolContext0.schoolContextModule' => 'Module A',
                'schoolContext0.schoolContextApplication' => 'Application A',
                'schoolContext1.schoolContextTeaching' => 'Teaching B',
                'schoolContext1.schoolContextModule' => 'Module B',
                'schoolContext1.schoolContextApplication' => 'Application B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);
        
        $this->assertArrayNotHasKey('schoolContext0.schoolContextCycle', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('schoolContext1.schoolContextCycle', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('schoolContext0.schoolContextConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('schoolContext1.schoolContextConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('schoolContext0.schoolContextMethod', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('schoolContext1.schoolContextMethod', $evaluation->result->fieldResults);
        
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext0.schoolContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextTeaching']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextModule']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextApplication']->isCorrect);
    }

    public function testIgnoresFieldsThatAreNotApplicableToAnItem(): void
    {
        $items = $this->defaultItems();
        $expected = $this->defaultExpected();

        unset($items[0]['schoolContextCycle']);
        unset($expected[0]['schoolContextCycle']);

        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true, true),
            $items,
            $expected
        );

        $stepAnswer = [
            'step' => ExerciseStep::SCHOOL_CONTEXT->value,
            'values' => [
                'schoolContext0.schoolContextTeaching' => 'Teaching A',
                'schoolContext0.schoolContextModule' => 'Module A',
                'schoolContext0.schoolContextConcept' => 'Concept A',
                'schoolContext0.schoolContextApplication' => 'Application A',
                'schoolContext0.schoolContextMethod' => 'Method A',
                'schoolContext1.schoolContextTeaching' => 'Teaching B',
                'schoolContext1.schoolContextCycle' => 'Cycle B',
                'schoolContext1.schoolContextModule' => 'Module B',
                'schoolContext1.schoolContextConcept' => 'Concept B',
                'schoolContext1.schoolContextApplication' => 'Application B',
                'schoolContext1.schoolContextMethod' => 'Method B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);

        $this->assertArrayNotHasKey('schoolContext0.schoolContextCycle', $evaluation->result->fieldResults);
         $this->assertTrue($evaluation->result->fieldResults['schoolContext1.schoolContextCycle']->isCorrect);
    }

    private function createService(): SchoolContextEvaluationService
    {
        return new SchoolContextEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            ),
            new DiceCoefficientSimilarityEvaluator()
        );
    }

    private function createPayload(
        array $fieldConfig,
        ?array $items = null,
        ?array $expected = null
    ): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::SCHOOL_CONTEXT->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => $items ?? $this->defaultItems(),
            StepPayloadKeys::EXPECTED => $expected ?? $this->defaultExpected()
        ];
    }

    private function createFieldConfig(
        bool $evalSchoolContextTeaching,
        bool $evalSchoolContextCycle,
        bool $evalSchoolContextModule,
        bool $evalSchoolContextConcept,
        bool $evalSchoolContextApplication,
        bool $evalSchoolContextMethod
    ): array
    {
        return  
            [
                'schoolContextTeaching' => [
                    'evaluable' => $evalSchoolContextTeaching,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'schoolContextCycle' => [
                    'evaluable' => $evalSchoolContextCycle,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'schoolContextModule' => [
                    'evaluable' => $evalSchoolContextModule,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'schoolContextConcept' => [
                    'evaluable' => $evalSchoolContextConcept,
                    'evaluationMode' => EvaluationMode::EQUALITY,
                ],
                'schoolContextApplication' => [
                    'evaluable' => $evalSchoolContextApplication,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],
                'schoolContextMethod' => [
                    'evaluable' => $evalSchoolContextMethod,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],                
            ];
    }

    private function defaultItems(): array
    {
        return  [
            [
                'key' => 'schoolContext0',
                'schoolContextTeaching' => [
                    'value' => 'Teaching A'
                ],
                'schoolContextCycle' => [
                    'value' => 'Cycle A'
                ],
                'schoolContextModule' => [
                    'value' => 'Module A'
                ],
                'schoolContextConcept' => [
                    'value' => 'Concept A'
                ],
                'schoolContextApplication' => [
                    'value' => 'Application A'
                ],
                'schoolContextMethod' => [
                    'value' => 'Method A'
                ]
            ],
            [
                'key' => 'schoolContext1',
                'schoolContextTeaching' => [
                    'value' => 'Teaching B'
                ],
                'schoolContextCycle' => [
                    'value' => 'Cycle B'
                ],
                'schoolContextModule' => [
                    'value' => 'Module B'
                ],
                'schoolContextConcept' => [
                    'value' => 'Concept B'
                ],
                'schoolContextApplication' => [
                    'value' => 'Application B'
                ],
                'schoolContextMethod' => [
                    'value' => 'Method B'
                ]                
            ],
        ];
    }

    private function defaultExpected(): array
    {
        return [
            [
                'key' => 'schoolContext0',
                'schoolContextTeaching' => 'Teaching A',
                'schoolContextCycle' => 'Cycle A',
                'schoolContextModule' => 'Module A',
                'schoolContextConcept' => 'Concept A',
                'schoolContextApplication' => 'Application A',
                'schoolContextMethod' => 'Method A'
            ],
            [
                'key' => 'schoolContext1',
                'schoolContextTeaching' => 'Teaching B',
                'schoolContextCycle' => 'Cycle B',
                'schoolContextModule' => 'Module B',
                'schoolContextConcept' => 'Concept B',
                'schoolContextApplication' => 'Application B',
                'schoolContextMethod' => 'Method B'
            ],                
        ];
    }
}