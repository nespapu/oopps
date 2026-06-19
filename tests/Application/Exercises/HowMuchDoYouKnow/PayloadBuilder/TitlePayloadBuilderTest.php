<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitlePayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeTopicRepository;

final class TitlePayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithHintAndMetadata(): void
    {
        $repo = new FakeTopicRepository('Sistemas operativos. Gestión de procesos', null);
        $hintService = new HintService();

        $builder = new TitlePayloadBuilder($repo, $hintService);

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(topicId: 16, difficulty: 2, flags: []),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertSame(ExerciseStep::TITLE->value, $payload['step']);

        $this->assertArrayHasKey(StepPayloadKeys::ITEMS, $payload);
        $this->assertSame('text', $payload[StepPayloadKeys::ITEMS][0]['type']);
        $this->assertSame('title', $payload[StepPayloadKeys::ITEMS][0]['name']);

        $this->assertArrayHasKey('hint', $payload[StepPayloadKeys::ITEMS][0]);
        $this->assertNotSame('', $payload[StepPayloadKeys::ITEMS][0]['hint']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertSame('Sistemas operativos. Gestión de procesos', $payload[StepPayloadKeys::META]['topicTitle']);
        $this->assertSame(HintMode::WORDS->value, $payload[StepPayloadKeys::META]['hintType']);
    }

    public function testUsesFallbackHintWhenTitleIsMissing(): void
    {
        $repo = new FakeTopicRepository(null, null);
        $hintService = new HintService();

        $builder = new TitlePayloadBuilder($repo, $hintService);

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(topicId: 16, difficulty: 2, flags: []),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        // If you changed the fallback string in the builder, update this expected value accordingly.
        $this->assertSame('(no hint generated)', $payload[StepPayloadKeys::ITEMS][0]['hint']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}