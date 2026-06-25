<?php


declare(strict_types=1);


namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexPayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeSectionRepository;


final class IndexPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithSectionsExpectedValuesAndMetadata(): void
    {
        $repo = new FakeSectionRepository([
            [
                'sectionOrder' => '1',
                'sectionTitle' => 'Introducción'
            ],
            [
                'sectionOrder' => '2',
                'sectionTitle' => 'Procesos'
            ]
        ]);

        $builder = new IndexPayloadBuilder($repo, new HintService());

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

        $this->assertSame(ExerciseStep::INDEX->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('item0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('1', $payload[StepPayloadKeys::ITEMS][0]['sectionOrder']);
        $this->assertSame('Introducción', $payload[StepPayloadKeys::ITEMS][0]['sectionTitle']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::ITEMS][0]['evaluation']['mode']);
        $this->assertSame('item1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('2', $payload[StepPayloadKeys::ITEMS][1]['sectionOrder']);
        $this->assertSame('Procesos', $payload[StepPayloadKeys::ITEMS][1]['sectionTitle']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::ITEMS][1]['evaluation']['mode']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['sectionOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['sectionTitle']);

        $this->assertSame('1', $payload[StepPayloadKeys::EXPECTED][0]['sectionOrder']);
        $this->assertSame('Introducción', $payload[StepPayloadKeys::EXPECTED][0]['sectionTitle']);
        $this->assertSame('2', $payload[StepPayloadKeys::EXPECTED][1]['sectionOrder']);
        $this->assertSame('Procesos', $payload[StepPayloadKeys::EXPECTED][1]['sectionTitle']);

    }

    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $repo = new FakeSectionRepository([
            [
                'sectionOrder' => '1',
                'sectionTitle' => 'Introducción',
            ],
        ]);

        $builder = new IndexPayloadBuilder($repo, new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'sectionOrder' => true,
                    'sectionTitle' => false,
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['evaluable']['sectionOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['evaluable']['sectionTitle']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}