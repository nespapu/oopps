<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Justification\JustificationEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class JustificationEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload([
            'cycles' => true,
            'laws' => true,
            'modules' => true
        ]);

        $stepAnswer = [
            'step' => ExerciseStep::JUSTIFICATION->value,
            'values' => [
                'cycle0.name' => 'Cycle A',
                'cycle0.law0.name' => 'Law A1',
                'cycle0.law1.name' => 'Law A2',
                'cycle0.module0.name' => 'Module A1',
                'cycle0.module1.name' => 'Module A2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.law0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.law1.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module1.name']->isCorrect);
    }

    public function testReturnsIncorrectWhenOneLawDoesNotMatch(): void
    {
        $payload = $this->createPayload([
            'cycles' => true,
            'laws' => true,
            'modules' => true
        ]);

        $stepAnswer = [
            'step' => ExerciseStep::JUSTIFICATION->value,
            'values' => [
                'cycle0.name' => 'Cycle A',
                'cycle0.law0.name' => 'Law A',
                'cycle0.law1.name' => 'Law A2',
                'cycle0.module0.name' => 'Module A1',
                'cycle0.module1.name' => 'Module A2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.name']->isCorrect);
        $this->assertFalse($evaluation->result->fieldResults['cycle0.law0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.law1.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module1.name']->isCorrect);
    }

    public function testReturnsIncorrectWhenOneModuleDoesNotMatch(): void
    {
        $payload = $this->createPayload([
            'cycles' => true,
            'laws' => true,
            'modules' => true
        ]);

        $stepAnswer = [
            'step' => ExerciseStep::JUSTIFICATION->value,
            'values' => [
                'cycle0.name' => 'Cycle A',
                'cycle0.law0.name' => 'Law A1',
                'cycle0.law1.name' => 'Law A2',
                'cycle0.module0.name' => 'Module A1',
                'cycle0.module1.name' => 'Module A'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.law0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.law1.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module0.name']->isCorrect);
        $this->assertFalse($evaluation->result->fieldResults['cycle0.module1.name']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload([
            'cycles' => true,
            'laws' => false,
            'modules' => true
        ]);

        $stepAnswer = [
            'step' => ExerciseStep::JUSTIFICATION->value,
            'values' => [
                'cycle0.name' => 'Cycle A',
                'cycle0.module0.name' => 'Module A1',
                'cycle0.module1.name' => 'Module A2'
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertArrayNotHasKey('cycle0.law0.name', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('cycle0.law1.name', $evaluation->result->fieldResults);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module0.name']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['cycle0.module1.name']->isCorrect);
    }

    private function createService(): JustificationEvaluationService
    {
        return new JustificationEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            )
        );
    }    

    private function createPayload(array $evaluable): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::JUSTIFICATION->value,
            StepPayloadKeys::META => [
                'evaluable' => $evaluable,
            ],
            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'cycle0',
                    'name' => 'Cycle A',
                    'evaluation' => [
                        'mode' => EvaluationMode::EQUALITY
                    ],
                    'laws' => [
                        [
                            'key' => 'law0',
                            'name' => 'Law A1',
                            'evaluation' => [
                                'mode' => EvaluationMode::EQUALITY
                            ]
                        ],
                        [
                            'key' => 'law1',
                            'name' => 'Law A2',
                            'evaluation' => [
                                'mode' => EvaluationMode::EQUALITY
                            ]
                        ]
                    ],
                    'modules' => [
                        [
                            'key' => 'module0',
                            'name' => 'Module A1',
                            'evaluation' => [
                                'mode' => EvaluationMode::EQUALITY
                            ]
                        ],
                        [
                            'key' => 'module1',
                            'name' => 'Module A2',
                            'evaluation' => [
                                'mode' => EvaluationMode::EQUALITY
                            ]
                        ]
                    ]
                ],
            ],
            StepPayloadKeys::EXPECTED => [
                [
                    'key' => 'cycle0',
                    'name' => 'cycle A',
                    'laws' => [
                        [
                            'key' => 'law0',
                            'name' => 'Law A1'
                        ],
                        [
                            'key' => 'law1',
                            'name' => 'Law A2'
                        ]
                    ],
                    'modules' => [
                        [
                            'key' => 'module0',
                            'name' => 'Module A1'
                        ],
                        [
                            'key' => 'module1',
                            'name' => 'Module A2'
                        ]
                    ]
                ]                
            ]
        ];
    }

}