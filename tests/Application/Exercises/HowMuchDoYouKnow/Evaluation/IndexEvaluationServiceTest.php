<?php


declare(strict_types=1);


namespace Tests\Application\Exercises\HowMuchDoYouKnow\Evaluation;

use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Domain\Exercise\ExerciseStep;
use PHPUnit\Framework\TestCase;


final class IndexEvaluationServiceTest extends TestCase
{
    public function testEvaluatesCorrectAnswer(): void
    {
        $payload = $this->createPayload([
            'sectionOrder' => true,
            'sectionTitle' => true
        ]);
    
        $answer = [
            'step' => ExerciseStep::INDEX->value,
            'values' => [
                'item0.sectionOrder' => '1',
                'item0.sectionTitle' => 'Introducción'
            ]
        ];             

        $indexEvaluationService = $this->createService();

        $evaluation = $indexEvaluationService->evaluate($payload, $answer);

        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertTrue($evaluation->result->fieldResults['item0.sectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['item0.sectionTitle']->isCorrect);
    }


    public function testEvaluatesIncorrectField(): void
    {
    
        $payload = $this->createPayload([
            'sectionOrder' => true,
            'sectionTitle' => true
        ]);

        $answer = [
            'step' => ExerciseStep::INDEX->value,
            'values' => [
                'item0.sectionOrder' => '2',
                'item0.sectionTitle' => 'Introducción'
            ]
        ];


        $indexEvaluationService = $this->createService();


        $evaluation = $indexEvaluationService->evaluate($payload, $answer);


        $this->assertFalse($evaluation->result->isStepCorrect);
        $this->assertFalse($evaluation->result->fieldResults['item0.sectionOrder']->isCorrect);
        $this->assertTrue($evaluation->result->fieldResults['item0.sectionTitle']->isCorrect);
    }


    public function testIgnoresNonEvaluableFields(): void
    {
        $payload = $this->createPayload([
            'sectionOrder' => false,
            'sectionTitle' => true
        ]);
    
        $answer = [
            'step' => ExerciseStep::INDEX->value,
            'values' => [
                'item0.sectionTitle' => 'Introducción'
            ]
        ];                

        $indexEvaluationService = $this->createService();

        $evaluation = $indexEvaluationService->evaluate($payload, $answer);


        $this->assertTrue($evaluation->result->isStepCorrect);
        $this->assertArrayNotHasKey('item0.sectionOrder', $evaluation->result->fieldResults);
        $this->assertTrue($evaluation->result->fieldResults['item0.sectionTitle']->isCorrect);
    }

    private function createService(): IndexEvaluationService
    {
        return new IndexEvaluationService(
            new EqualityEvaluator(
                new TextNormalizer()
            )
        );
    }

    private function createPayload(array $evaluable): array
    {
        return [
            StepPayloadKeys::STEP => ExerciseStep::INDEX->value,
            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'item0',
                ],
            ],
            StepPayloadKeys::EXPECTED => [
                [
                    'key' => 'item0',
                    'sectionOrder' => '1',
                    'sectionTitle' => 'Introducción',
                ],
            ],
            StepPayloadKeys::META => [
                'evaluable' => $evaluable,
            ],
        ];
    }
}