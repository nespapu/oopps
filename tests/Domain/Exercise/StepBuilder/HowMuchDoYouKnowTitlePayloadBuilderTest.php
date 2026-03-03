<?php

declare(strict_types=1);

namespace Tests\Domain\Exercise\StepBuilder;

use App\Application\Exercises\Payload\StepPayloadKeys;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowTitlePayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Domain\Exercise\Doubles\FakeTopicRepository;

final class HowMuchDoYouKnowTitlePayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithHintAndMetadata(): void
    {
        $repo = new FakeTopicRepository('Sistemas operativos. Gestión de procesos', null);
        $hintService = new HintService();

        $builder = new HowMuchDoYouKnowTitlePayloadBuilder($repo, $hintService);

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(topicId: 16, difficulty: 2, flags: []),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertSame(ExerciseStep::TITLE->value, $payload['step']);

        $this->assertArrayHasKey(StepPayloadKeys::ITEMS, $payload);
        $this->assertSame('text', $payload[StepPayloadKeys::ITEMS][0]['tipo']);
        $this->assertSame('titulo', $payload[StepPayloadKeys::ITEMS][0]['nombre']);

        $this->assertArrayHasKey('pista', $payload[StepPayloadKeys::ITEMS][0]);
        $this->assertNotSame('', $payload[StepPayloadKeys::ITEMS][0]['pista']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['numeracionTema']);
        $this->assertSame('Sistemas operativos. Gestión de procesos', $payload[StepPayloadKeys::META]['tituloTema']);
        $this->assertSame(HintMode::WORDS->value, $payload[StepPayloadKeys::META]['tipoPista']);
    }

    public function testUsesFallbackHintWhenTitleIsMissing(): void
    {
        $repo = new FakeTopicRepository(null, null);
        $hintService = new HintService();

        $builder = new HowMuchDoYouKnowTitlePayloadBuilder($repo, $hintService);

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(topicId: 16, difficulty: 2, flags: []),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        // If you changed the fallback string in the builder, update this expected value accordingly.
        $this->assertSame('(no hint generated)', $payload[StepPayloadKeys::ITEMS][0]['pista']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}