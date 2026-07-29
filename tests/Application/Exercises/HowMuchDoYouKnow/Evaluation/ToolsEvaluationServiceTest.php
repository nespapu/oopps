<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\Tools\ToolsEvaluationService;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class ToolsEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::TOOLS->value,
            'values' => [
                'tool0.toolName' => 'Tool A',
                'tool0.toolDescription' => 'Description tool A',
                'tool1.toolName' => 'Tool B',
                'tool1.toolDescription' => 'Description tool B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool0.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool0.toolDescription']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolDescription']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::TOOLS->value,
            'values' => [
                'tool0.toolName' => 'Tool C',
                'tool0.toolDescription' => 'Description tool A',
                'tool1.toolName' => 'Tool B',
                'tool1.toolDescription' => 'Description tool B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['tool0.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool0.toolDescription']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolDescription']->isCorrect);
    }

    public function testReturnsIncorrectWhenSimilarityScoreIsBelowThreshold(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::TOOLS->value,
            'values' => [
                'tool0.toolName' => 'Tool A',
                'tool0.toolDescription' => 'Descri',
                'tool1.toolName' => 'Tool B',
                'tool1.toolDescription' => 'Description tool B'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);

        $fieldResult = $evaluation->result->fieldResults['tool0.toolDescription'];
        $this->assertNotNull($fieldResult->similarityScore);
        $this->assertLessThan(0.8, $fieldResult->similarityScore);
        $this->assertFalse($fieldResult->isCorrect);
        
        $this->assertTrue($evaluation->result->fieldResults['tool0.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolDescription']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(false, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::TOOLS->value,
            'values' => [
                'tool0.toolDescription' => 'Description tool A',
                'tool1.toolDescription' => 'Description tool B'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
       
        $this->assertArrayNotHasKey('tool0.toolName', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('tool1.toolName', $evaluation->result->fieldResults);
        
        $this->assertTrue($evaluation->result->fieldResults['tool0.toolDescription']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['tool1.toolDescription']->isCorrect);
    }
    
    private function createService(): ToolsEvaluationService
    {
        return new ToolsEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            ),
            new DiceCoefficientSimilarityEvaluator()
        );
    }
    
    private function createPayload(array $fieldConfig): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::TOOLS->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'tool0',
                    'toolName' => [
                        'value' => 'Tool A'
                    ],
                    'toolDescription' => [
                        'value' => 'Description tool A'
                    ]
                ],
                [
                    'key' => 'tool1',
                    'toolName' => [
                        'value' => 'Tool B'
                    ],
                    'toolDescription' => [
                        'value' => 'Description tool B'
                    ]
                ]
            ],
            StepPayloadKeys::EXPECTED => [
                [
                    'key' => 'tool0',
                    'toolName' => 'Tool A',
                    'toolDescription' => 'Description tool A'
                ],
                [
                    'key' => 'tool1',
                    'toolName' => 'Tool B',
                    'toolDescription' => 'Description tool B'
                ]
            ]
        ];
    }

    private function createFieldConfig(
        bool $evalToolName,
        bool $evalToolDescription,
    ): array
    {
        return  
            [
                'toolName' => [
                    'evaluable' => $evalToolName,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'toolDescription' => [
                    'evaluable' => $evalToolDescription,
                    'evaluationMode' => EvaluationMode::SIMILARITY,
                    'threshold' => 0.8
                ],
            ];
    }
}