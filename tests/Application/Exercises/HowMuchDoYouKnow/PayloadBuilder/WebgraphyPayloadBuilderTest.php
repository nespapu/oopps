<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Webgraphy\WebgraphyPayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeWebsiteRepository;

final class WebgraphyPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithWebgraphyExpectedValuesAndMetadata(): void
    {
        $builder = new WebgraphyPayloadBuilder(new FakeWebsiteRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::WEBGRAPHY->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('website0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Website A', $payload[StepPayloadKeys::ITEMS][0]['websiteName']['value']);
        $this->assertSame('URL A', $payload[StepPayloadKeys::ITEMS][0]['websiteURL']['value']);
        $this->assertSame('website1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Website B', $payload[StepPayloadKeys::ITEMS][1]['websiteName']['value']);
        $this->assertSame('URL B', $payload[StepPayloadKeys::ITEMS][1]['websiteURL']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['websiteName']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['websiteName']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['websiteName']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['websiteURL']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['websiteURL']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['websiteURL']['hintMode']);

        $this->assertSame('website0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Website A', $payload[StepPayloadKeys::EXPECTED][0]['websiteName']);
        $this->assertSame('URL A', $payload[StepPayloadKeys::EXPECTED][0]['websiteURL']);

        $this->assertSame('website1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Website B', $payload[StepPayloadKeys::EXPECTED][1]['websiteName']);
        $this->assertSame('URL B', $payload[StepPayloadKeys::EXPECTED][1]['websiteURL']);
    }

    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new WebgraphyPayloadBuilder(new FakeWebsiteRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'websiteName' => true,
                    'websiteURL' => false,
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['websiteName']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['websiteURL']['evaluable']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}