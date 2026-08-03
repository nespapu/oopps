<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextEvaluationService;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class WorkContextEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WORK_CONTEXT->value,
            'values' => [
                'workContext0.workContextField' => 'Field A',
                'workContext0.workContextRole' => 'Role A',
                'workContext0.workContextConcept' => 'Concept A',
                'workContext0.workContextApplication' => 'Application A',
                'workContext0.workContextBenefit' => 'Benefit A',
                'workContext1.workContextField' => 'Field B',
                'workContext1.workContextRole' => 'Role B',
                'workContext1.workContextConcept' => 'Concept B',
                'workContext1.workContextApplication' => 'Application B',
                'workContext1.workContextBenefit' => 'Benefit B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextBenefit']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextBenefit']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WORK_CONTEXT->value,
            'values' => [
                'workContext0.workContextField' => 'Field',
                'workContext0.workContextRole' => 'Role A',
                'workContext0.workContextConcept' => 'Concept A',
                'workContext0.workContextApplication' => 'Application A',
                'workContext0.workContextBenefit' => 'Benefit A',
                'workContext1.workContextField' => 'Field B',
                'workContext1.workContextRole' => 'Role B',
                'workContext1.workContextConcept' => 'Concept B',
                'workContext1.workContextApplication' => 'Application B',
                'workContext1.workContextBenefit' => 'Benefit B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['workContext0.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextBenefit']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextBenefit']->isCorrect);
    }

    public function testReturnsIncorrectWhenSimilarityScoreIsBelowThreshold(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WORK_CONTEXT->value,
            'values' => [
                'workContext0.workContextField' => 'Field A',
                'workContext0.workContextRole' => 'Role A',
                'workContext0.workContextConcept' => 'Concept A',
                'workContext0.workContextApplication' => 'Ap',
                'workContext0.workContextBenefit' => 'Benefit A',
                'workContext1.workContextField' => 'Field B',
                'workContext1.workContextRole' => 'Role B',
                'workContext1.workContextConcept' => 'Concept B',
                'workContext1.workContextApplication' => 'Application B',
                'workContext1.workContextBenefit' => 'Benefit B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        
        $fieldResult = $evaluation->result->fieldResults['workContext0.workContextApplication'];
        $this->assertNotNull($fieldResult->similarityScore);
        $this->assertLessThan(0.8, $fieldResult->similarityScore);
        $this->assertFalse($fieldResult->isCorrect);

        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextBenefit']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextField']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextConcept']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextBenefit']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(false, true, false, true, false)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WORK_CONTEXT->value,
            'values' => [
                'workContext0.workContextRole' => 'Role A',
                'workContext0.workContextApplication' => 'Application A',
                'workContext1.workContextRole' => 'Role B',
                'workContext1.workContextApplication' => 'Application B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);
        
        $this->assertArrayNotHasKey('workContext0.workContextField', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('workContext1.workContextField', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('workContext0.workContextConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('workContext1.workContextConcept', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('workContext0.workContextBenefit', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('workContext1.workContextBenefit', $evaluation->result->fieldResults);
        
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext0.workContextApplication']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextRole']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextApplication']->isCorrect);
    }

    public function testIgnoresFieldsThatAreNotApplicableToAnItem(): void
    {
        $items = $this->defaultItems();
        $expected = $this->defaultExpected();

        unset($items[0]['workContextRole']);
        unset($expected[0]['workContextRole']);

        $payload = $this->createPayload(
            $this->createFieldConfig(true, true, true, true, true),
            $items,
            $expected
        );

        $stepAnswer = [
            'step' => ExerciseStep::WORK_CONTEXT->value,
            'values' => [
                'workContext0.workContextField' => 'Field A',
                'workContext0.workContextConcept' => 'Concept A',
                'workContext0.workContextApplication' => 'Application A',
                'workContext0.workContextBenefit' => 'Benefit A',
                'workContext1.workContextField' => 'Field B',
                'workContext1.workContextRole' => 'Role B',
                'workContext1.workContextConcept' => 'Concept B',
                'workContext1.workContextApplication' => 'Application B',
                'workContext1.workContextBenefit' => 'Benefit B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);

        $this->assertArrayNotHasKey('workContext0.workContextRole', $evaluation->result->fieldResults);
        $this->assertTrue($evaluation->result->fieldResults['workContext1.workContextRole']->isCorrect);
    }

    private function createService(): WorkContextEvaluationService
    {
        return new WorkContextEvaluationService(
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
            StepPayloadKeys::STEP => ExerciseStep::WORK_CONTEXT->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => $items ?? $this->defaultItems(),
            StepPayloadKeys::EXPECTED => $expected ?? $this->defaultExpected()
        ];
    }

    private function createFieldConfig(
        bool $evalWorkContextField,
        bool $evalWorkContextRole,
        bool $evalWorkContextConcept,
        bool $evalWorkContextApplication,
        bool $evalWorkContextBenefit
    ): array
    {
        return  
            [
                'workContextField' => [
                    'evaluable' => $evalWorkContextField,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'workContextRole' => [
                    'evaluable' => $evalWorkContextRole,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'workContextConcept' => [
                    'evaluable' => $evalWorkContextConcept,
                    'evaluationMode' => EvaluationMode::EQUALITY,
                ],
                'workContextApplication' => [
                    'evaluable' => $evalWorkContextApplication,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],
                'workContextBenefit' => [
                    'evaluable' => $evalWorkContextBenefit,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],                
            ];
    }

    private function defaultItems(): array
    {
        return  [
            [
                'key' => 'workContext0',
                'workContextField' => [
                    'value' => 'Field A'
                ],
                'workContextRole' => [
                    'value' => 'Role A'
                ],
                'workContextConcept' => [
                    'value' => 'Concept A'
                ],
                'workContextApplication' => [
                    'value' => 'Application A'
                ],
                'workContextBenefit' => [
                    'value' => 'Benefit A'
                ]
            ],
            [
                'key' => 'workContext1',
                'workContextField' => [
                    'value' => 'Field B'
                ],
                'workContextRole' => [
                    'value' => 'Role B'
                ],
                'workContextConcept' => [
                    'value' => 'Concept B'
                ],
                'workContextApplication' => [
                    'value' => 'Application B'
                ],
                'workContextBenefit' => [
                    'value' => 'Benefit B'
                ]
            ],
        ];
    }

    private function defaultExpected(): array
    {
        return [
            [
                'key' => 'workContext0',
                'workContextField' => 'Field A',
                'workContextRole' => 'Role A',
                'workContextConcept' => 'Concept A',
                'workContextApplication' => 'Application A',
                'workContextBenefit' => 'Benefit A'
            ],
            [
                'key' => 'workContext1',
                'workContextField' => 'Field B',
                'workContextRole' => 'Role B',
                'workContextConcept' => 'Concept B',
                'workContextApplication' => 'Application B',
                'workContextBenefit' => 'Benefit B'
            ],                
        ];
    }
}