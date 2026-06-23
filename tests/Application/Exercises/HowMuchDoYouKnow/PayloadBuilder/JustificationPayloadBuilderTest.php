<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\HowMuchDoYouKnow\Justification\JustificationPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeJustificationRepository;

final class JustificationPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithHintsAndExpectedValues(): void
    {
        $builder = new JustificationPayloadBuilder(new FakeJustificationRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::JUSTIFICATION->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('cycle0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Cycle A', $payload[StepPayloadKeys::ITEMS][0]['name']);
        $this->assertSame('Cyc__ A', $payload[StepPayloadKeys::ITEMS][0]['hint']);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS][0]['laws']);
        $this->assertSame('law0', $payload[StepPayloadKeys::ITEMS][0]['laws'][0]['key']);
        $this->assertSame('Law A1', $payload[StepPayloadKeys::ITEMS][0]['laws'][0]['name']);
        $this->assertSame('La_ A_', $payload[StepPayloadKeys::ITEMS][0]['laws'][0]['hint']);
        $this->assertSame('law1', $payload[StepPayloadKeys::ITEMS][0]['laws'][1]['key']);
        $this->assertSame('Law A2', $payload[StepPayloadKeys::ITEMS][0]['laws'][1]['name']);
        $this->assertSame('La_ A_', $payload[StepPayloadKeys::ITEMS][0]['laws'][1]['hint']);
        $this->assertCount(1, $payload[StepPayloadKeys::ITEMS][0]['modules']);
        $this->assertSame('module0', $payload[StepPayloadKeys::ITEMS][0]['modules'][0]['key']);
        $this->assertSame('Module A1', $payload[StepPayloadKeys::ITEMS][0]['modules'][0]['name']);
        $this->assertSame('Mod___ A_', $payload[StepPayloadKeys::ITEMS][0]['modules'][0]['hint']);
        $this->assertSame('cycle1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Cycle B', $payload[StepPayloadKeys::ITEMS][1]['name']);
        $this->assertSame('Cyc__ B', $payload[StepPayloadKeys::ITEMS][1]['hint']);
        $this->assertCount(1, $payload[StepPayloadKeys::ITEMS][1]['laws']);
        $this->assertSame('law0', $payload[StepPayloadKeys::ITEMS][1]['laws'][0]['key']);
        $this->assertSame('Law B1', $payload[StepPayloadKeys::ITEMS][1]['laws'][0]['name']);
        $this->assertSame('La_ B_', $payload[StepPayloadKeys::ITEMS][1]['laws'][0]['hint']);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS][1]['modules']);
        $this->assertSame('module0', $payload[StepPayloadKeys::ITEMS][1]['modules'][0]['key']);
        $this->assertSame('Module B1', $payload[StepPayloadKeys::ITEMS][1]['modules'][0]['name']);
        $this->assertSame('Mod___ B_', $payload[StepPayloadKeys::ITEMS][1]['modules'][0]['hint']);
        $this->assertSame('module1', $payload[StepPayloadKeys::ITEMS][1]['modules'][1]['key']);
        $this->assertSame('Module B2', $payload[StepPayloadKeys::ITEMS][1]['modules'][1]['name']);
        $this->assertSame('Mod___ B_', $payload[StepPayloadKeys::ITEMS][1]['modules'][1]['hint']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['cycles']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['laws']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['modules']);

        $this->assertCount(2, $payload[StepPayloadKeys::EXPECTED]);
        $this->assertSame('cycle0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Cycle A', $payload[StepPayloadKeys::EXPECTED][0]['name']);
        $this->assertCount(2, $payload[StepPayloadKeys::EXPECTED][0]['laws']);
        $this->assertSame('law0', $payload[StepPayloadKeys::EXPECTED][0]['laws'][0]['key']);
        $this->assertSame('Law A1', $payload[StepPayloadKeys::EXPECTED][0]['laws'][0]['name']);
        $this->assertSame('law1', $payload[StepPayloadKeys::EXPECTED][0]['laws'][1]['key']);
        $this->assertSame('Law A2', $payload[StepPayloadKeys::EXPECTED][0]['laws'][1]['name']);
        $this->assertCount(1, $payload[StepPayloadKeys::EXPECTED][0]['modules']);
        $this->assertSame('module0', $payload[StepPayloadKeys::EXPECTED][0]['modules'][0]['key']);
        $this->assertSame('Module A1', $payload[StepPayloadKeys::EXPECTED][0]['modules'][0]['name']);
        $this->assertSame('cycle1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Cycle B', $payload[StepPayloadKeys::EXPECTED][1]['name']);
        $this->assertCount(1, $payload[StepPayloadKeys::EXPECTED][1]['laws']);
        $this->assertSame('law0', $payload[StepPayloadKeys::EXPECTED][1]['laws'][0]['key']);
        $this->assertSame('Law B1', $payload[StepPayloadKeys::EXPECTED][1]['laws'][0]['name']);
        $this->assertCount(2, $payload[StepPayloadKeys::EXPECTED][1]['modules']);
        $this->assertSame('module0', $payload[StepPayloadKeys::EXPECTED][1]['modules'][0]['key']);
        $this->assertSame('Module B1', $payload[StepPayloadKeys::EXPECTED][1]['modules'][0]['name']);
        $this->assertSame('module1', $payload[StepPayloadKeys::EXPECTED][1]['modules'][1]['key']);
        $this->assertSame('Module B2', $payload[StepPayloadKeys::EXPECTED][1]['modules'][1]['name']);
    }


    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {

        $builder = new JustificationPayloadBuilder(new FakeJustificationRepository(), new HintService());


        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'cycles' => false,
                    'laws' => true,
                    'modules' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );


        $payload = $builder->build($session);

        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['cycles']);
        $this->assertFalse($payload[StepPayloadKeys::META]['evaluable']['laws']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['modules']);
    }


    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}