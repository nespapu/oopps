<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Tools\ToolsPayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeToolsRepository;


final class ToolsPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithToolsExpectedValuesAndMetadata(): void
    {
        $builder = new ToolsPayloadBuilder(new FakeToolsRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::TOOLS->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('tool0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Tool A', $payload[StepPayloadKeys::ITEMS][0]['toolName']['value']);
        $this->assertSame('Description tool A', $payload[StepPayloadKeys::ITEMS][0]['toolDescription']['value']);
        $this->assertSame('tool1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Tool B', $payload[StepPayloadKeys::ITEMS][1]['toolName']['value']);
        $this->assertSame('Description tool B', $payload[StepPayloadKeys::ITEMS][1]['toolDescription']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['toolName']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['toolName']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['toolName']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['toolDescription']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['toolDescription']['evaluationMode']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['toolDescription']['hintMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['toolDescription']['threshold']);

        $this->assertSame('tool0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Tool A', $payload[StepPayloadKeys::EXPECTED][0]['toolName']);
        $this->assertSame('Description tool A', $payload[StepPayloadKeys::EXPECTED][0]['toolDescription']);
        $this->assertSame('tool1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Tool B', $payload[StepPayloadKeys::EXPECTED][1]['toolName']);
        $this->assertSame('Description tool B', $payload[StepPayloadKeys::EXPECTED][1]['toolDescription']);
    }

    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new ToolsPayloadBuilder(new FakeToolsRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'toolName' => true,
                    'toolDescription' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['toolName']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['toolDescription']['evaluable']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}