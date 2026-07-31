<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\SchoolContext\SchoolContextPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeSchoolContextRepository;

final class SchoolContextPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithSchoolContextExpectedValuesAndMetadata(): void
    {
        $builder = new SchoolContextPayloadBuilder(new FakeSchoolContextRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::SCHOOL_CONTEXT->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('schoolContext0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Teaching A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextTeaching']['value']);
        $this->assertSame('Cycle A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextCycle']['value']);
        $this->assertSame('Module A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextModule']['value']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextConcept']['value']);
        $this->assertSame('Application A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextApplication']['value']);
        $this->assertSame('Method A', $payload[StepPayloadKeys::ITEMS][0]['schoolContextMethod']['value']);
        $this->assertSame('schoolContext1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Teaching B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextTeaching']['value']);
        $this->assertSame('Cycle B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextCycle']['value']);
        $this->assertSame('Module B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextModule']['value']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextConcept']['value']);
        $this->assertSame('Application B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextApplication']['value']);
        $this->assertSame('Method B', $payload[StepPayloadKeys::ITEMS][1]['schoolContextMethod']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextTeaching']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextTeaching']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextTeaching']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextCycle']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextCycle']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextCycle']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextModule']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextModule']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextModule']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextConcept']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextConcept']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextConcept']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextApplication']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextApplication']['evaluationMode']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextApplication']['hintMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextApplication']['threshold']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextMethod']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextMethod']['evaluationMode']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextMethod']['hintMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['schoolContextMethod']['threshold']);

        $this->assertSame('schoolContext0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Teaching A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextTeaching']);
        $this->assertSame('Cycle A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextCycle']);
        $this->assertSame('Module A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextModule']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextConcept']);
        $this->assertSame('Application A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextApplication']);
        $this->assertSame('Method A', $payload[StepPayloadKeys::EXPECTED][0]['schoolContextMethod']);
        $this->assertSame('schoolContext1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Teaching B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextTeaching']);
        $this->assertSame('Cycle B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextCycle']);
        $this->assertSame('Module B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextModule']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextConcept']);
        $this->assertSame('Application B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextApplication']);
        $this->assertSame('Method B', $payload[StepPayloadKeys::EXPECTED][1]['schoolContextMethod']);
    }

    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new SchoolContextPayloadBuilder(new FakeSchoolContextRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'schoolContextTeaching' => true,
                    'schoolContextCycle' => false,
                    'schoolContextModule' => true,
                    'schoolContextConcept' => true,
                    'schoolContextApplication' => false,
                    'schoolContextMethod' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextTeaching']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextCycle']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextModule']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextConcept']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextApplication']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['schoolContextMethod']['evaluable']);
    }

    public function testOmitsNotApplicableFieldsFromItemAndExpectedValues(): void
    {
        $schoolContext = [
            [
                'schoolContextTeaching' => 'Teaching A',
                'schoolContextCycle' => null,
                'schoolContextModule' => 'Module A',
                'schoolContextConcept' => 'Concept A',
                'schoolContextApplication' => 'Application A',
                'schoolContextMethod' => 'Method A',
            ],
            [
                'schoolContextTeaching' => 'Teaching B',
                'schoolContextCycle' => 'Cycle B',
                'schoolContextModule' => 'Module B',
                'schoolContextConcept' => 'Concept B',
                'schoolContextApplication' => 'Application B',
                'schoolContextMethod' => 'Method B',
            ]
        ];

        $builder = new SchoolContextPayloadBuilder(new FakeSchoolContextRepository($schoolContext), new HintService());

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

        $this->assertArrayNotHasKey('schoolContextCycle', $payload[StepPayloadKeys::ITEMS][0]);
        $this->assertArrayHasKey('schoolContextCycle', $payload[StepPayloadKeys::ITEMS][1]);
        $this->assertArrayNotHasKey('schoolContextCycle', $payload[StepPayloadKeys::EXPECTED][0]);
        $this->assertArrayHasKey('schoolContextCycle', $payload[StepPayloadKeys::EXPECTED][1]);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}