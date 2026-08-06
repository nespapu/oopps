<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\Webgraphy\WebgraphyEvaluationService;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;

final class WebgraphyEvaluationServiceTest extends TestCase
{
    public function testReturnsCorrectWhenAllEvaluableFieldsMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WEBGRAPHY->value,
            'values' => [
                'website0.websiteName' => 'Website A',
                'website0.websiteURL' => 'URL A',
                'website1.websiteName' => 'Website B',
                'website1.websiteURL' => 'URL B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website0.websiteName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website0.websiteURL']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website1.websiteName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website1.websiteURL']->isCorrect);
    }

    public function testReturnsIncorrectWhenEqualityFieldDoesNotMatch(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(true, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WEBGRAPHY->value,
            'values' => [
                'website0.websiteName' => 'Website',
                'website0.websiteURL' => 'URL A',
                'website1.websiteName' => 'Website B',
                'website1.websiteURL' => 'URL B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);

        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['website0.websiteName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website0.websiteURL']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website1.websiteName']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website1.websiteURL']->isCorrect);
    }

    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload(
            $this->createFieldConfig(false, true)
        );

        $stepAnswer = [
            'step' => ExerciseStep::WEBGRAPHY->value,
            'values' => [
                'website0.websiteURL' => 'URL A',
                'website1.websiteURL' => 'URL B',
            ]
        ];

        $service = $this->createService();

        $evaluation = $service->evaluate($payload, $stepAnswer);      

        $this->assertTrue($evaluation->result->isStepCorrect);
        
        $this->assertArrayNotHasKey('website0.websiteName', $evaluation->result->fieldResults);
        $this->assertArrayNotHasKey('website1.websiteName', $evaluation->result->fieldResults);
    
        $this->assertTrue($evaluation->result->fieldResults['website0.websiteURL']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['website1.websiteURL']->isCorrect);
    }

    private function createService(): WebgraphyEvaluationService
    {
        return new WebgraphyEvaluationService(
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
            StepPayloadKeys::STEP => ExerciseStep::WEBGRAPHY->value,
            StepPayloadKeys::META => [
                'fieldConfig' => $fieldConfig
            ],
            StepPayloadKeys::ITEMS => $items ?? $this->defaultItems(),
            StepPayloadKeys::EXPECTED => $expected ?? $this->defaultExpected()
        ];
    }

    private function createFieldConfig(
        bool $evalWebsiteName,
        bool $evalWebsiteURL
    ): array
    {
        return  
            [
                'websiteName' => [
                    'evaluable' => $evalWebsiteName,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ],
                'websiteURL' => [
                    'evaluable' => $evalWebsiteURL,
                    'evaluationMode' => EvaluationMode::EQUALITY
                ]
            ];
    }

    private function defaultItems(): array
    {
        return  [
            [
                'key' => 'website0',
                'websiteName' => [
                    'value' => 'Website A'
                ],
                'websiteURL' => [
                    'value' => 'URL A'
                ]
            ],
            [
                'key' => 'website1',
                'websiteName' => [
                    'value' => 'Website B'
                ],
                'websiteURL' => [
                    'value' => 'URL B'
                ]
            ],
        ];
    }

    private function defaultExpected(): array
    {
        return [
            [
                'key' => 'website0',
                'websiteName' => 'Website A',
                'websiteURL' => 'URL A',
            ],
            [
                'key' => 'website1',
                'websiteName' => 'Website B',
                'websiteURL' => 'URL B',
            ],                
        ];
    }
}