<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextPayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeWorkContextRepository;

final class WorkContextPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithWorkContextExpectedValuesAndMetadata(): void
    {
        $builder = new WorkContextPayloadBuilder(new FakeWorkContextRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16, 
                difficulty: 2, 
                flags: []),
            firstStep: ExerciseStep::first()
        );
     
        $payload = $builder->build($session);

        $this->assertSame(ExerciseStep::WORK_CONTEXT->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('workContext0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Field A', $payload[StepPayloadKeys::ITEMS][0]['workContextField']['value']);
        $this->assertSame('Role A', $payload[StepPayloadKeys::ITEMS][0]['workContextRole']['value']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::ITEMS][0]['workContextConcept']['value']);
        $this->assertSame('Application A', $payload[StepPayloadKeys::ITEMS][0]['workContextApplication']['value']);
        $this->assertSame('Benefit A', $payload[StepPayloadKeys::ITEMS][0]['workContextBenefit']['value']);
        $this->assertSame('workContext1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Field B', $payload[StepPayloadKeys::ITEMS][1]['workContextField']['value']);
        $this->assertSame('Role B', $payload[StepPayloadKeys::ITEMS][1]['workContextRole']['value']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::ITEMS][1]['workContextConcept']['value']);
        $this->assertSame('Application B', $payload[StepPayloadKeys::ITEMS][1]['workContextApplication']['value']);
        $this->assertSame('Benefit B', $payload[StepPayloadKeys::ITEMS][1]['workContextBenefit']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextField']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['workContextField']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['workContextField']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextRole']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['workContextRole']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['workContextRole']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextConcept']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['workContextConcept']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['workContextConcept']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextApplication']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['workContextApplication']['evaluationMode']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['workContextApplication']['hintMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['workContextApplication']['threshold']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextBenefit']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['workContextBenefit']['evaluationMode']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['workContextBenefit']['hintMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['workContextBenefit']['threshold']);

        $this->assertSame('workContext0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Field A', $payload[StepPayloadKeys::EXPECTED][0]['workContextField']);
        $this->assertSame('Role A', $payload[StepPayloadKeys::EXPECTED][0]['workContextRole']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::EXPECTED][0]['workContextConcept']);
        $this->assertSame('Application A', $payload[StepPayloadKeys::EXPECTED][0]['workContextApplication']);
        $this->assertSame('Benefit A', $payload[StepPayloadKeys::EXPECTED][0]['workContextBenefit']);
        $this->assertSame('workContext1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Field B', $payload[StepPayloadKeys::EXPECTED][1]['workContextField']);
        $this->assertSame('Role B', $payload[StepPayloadKeys::EXPECTED][1]['workContextRole']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::EXPECTED][1]['workContextConcept']);
        $this->assertSame('Application B', $payload[StepPayloadKeys::EXPECTED][1]['workContextApplication']);
        $this->assertSame('Benefit B', $payload[StepPayloadKeys::EXPECTED][1]['workContextBenefit']);
    }

    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new WorkContextPayloadBuilder(new FakeWorkContextRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'workContextField' => true,
                    'workContextRole' => false,
                    'workContextConcept' => true,
                    'workContextApplication' => false,
                    'workContextBenefit' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['workContextField']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextRole']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['workContextConcept']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextApplication']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['workContextBenefit']['evaluable']);
    }

    public function testOmitsNotApplicableFieldsFromItemAndExpectedValues(): void
    {
        $workContext = [
            [
                'workContextField' => 'Field A',
                'workContextRole' => null,
                'workContextConcept' => 'Concept A',
                'workContextApplication' => 'Application A',
                'workContextBenefit' => 'Benefit A',
            ],
            [
                'workContextField' => 'Field B',
                'workContextRole' => 'Role B',
                'workContextConcept' => 'Concept B',
                'workContextApplication' => 'Application B',
                'workContextBenefit' => 'Benefit B',
            ]
        ];

        $builder = new WorkContextPayloadBuilder(new FakeWorkContextRepository($workContext), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16, 
                difficulty: 2, 
                flags: []),
            firstStep: ExerciseStep::first()
        );
     
        $payload = $builder->build($session);       

        $this->assertArrayNotHasKey('workContextRole', $payload[StepPayloadKeys::ITEMS][0]);
        $this->assertArrayHasKey('workContextRole', $payload[StepPayloadKeys::ITEMS][1]);
        $this->assertArrayNotHasKey('workContextRole', $payload[StepPayloadKeys::EXPECTED][0]);
        $this->assertArrayHasKey('workContextRole', $payload[StepPayloadKeys::EXPECTED][1]);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}