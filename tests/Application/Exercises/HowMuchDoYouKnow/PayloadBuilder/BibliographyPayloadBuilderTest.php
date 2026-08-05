<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Bibliography\BibliographyPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeBookRepository;

final class BibliographyPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithBibliographyExpectedValuesAndMetadata(): void
    {
        $builder = new BibliographyPayloadBuilder(new FakeBookRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::BIBLIOGRAPHY->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('book0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Author A', $payload[StepPayloadKeys::ITEMS][0]['bookAuthor']['value']);
        $this->assertSame('Year A', $payload[StepPayloadKeys::ITEMS][0]['bookPublicationYear']['value']);
        $this->assertSame('Title A', $payload[StepPayloadKeys::ITEMS][0]['bookTitle']['value']);
        $this->assertSame('Publisher A', $payload[StepPayloadKeys::ITEMS][0]['bookPublisher']['value']);
        $this->assertSame('book1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Author B', $payload[StepPayloadKeys::ITEMS][1]['bookAuthor']['value']);
        $this->assertSame('Year B', $payload[StepPayloadKeys::ITEMS][1]['bookPublicationYear']['value']);
        $this->assertSame('Title B', $payload[StepPayloadKeys::ITEMS][1]['bookTitle']['value']);
        $this->assertSame('Publisher B', $payload[StepPayloadKeys::ITEMS][1]['bookPublisher']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookAuthor']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['bookAuthor']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['bookAuthor']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookPublicationYear']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['bookPublicationYear']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['bookPublicationYear']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookTitle']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['bookTitle']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['bookTitle']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookPublisher']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['bookPublisher']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['bookPublisher']['hintMode']);

        $this->assertSame('book0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Author A', $payload[StepPayloadKeys::EXPECTED][0]['bookAuthor']);
        $this->assertSame('Year A', $payload[StepPayloadKeys::EXPECTED][0]['bookPublicationYear']);
        $this->assertSame('Title A', $payload[StepPayloadKeys::EXPECTED][0]['bookTitle']);
        $this->assertSame('Publisher A', $payload[StepPayloadKeys::EXPECTED][0]['bookPublisher']);

        $this->assertSame('book1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Author B', $payload[StepPayloadKeys::EXPECTED][1]['bookAuthor']);
        $this->assertSame('Year B', $payload[StepPayloadKeys::EXPECTED][1]['bookPublicationYear']);
        $this->assertSame('Title B', $payload[StepPayloadKeys::EXPECTED][1]['bookTitle']);
        $this->assertSame('Publisher B', $payload[StepPayloadKeys::EXPECTED][1]['bookPublisher']);
    }


    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new BibliographyPayloadBuilder(new FakeBookRepository(), new HintService());

        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'bookAuthor' => true,
                    'bookPublicationYear' => false,
                    'bookTitle' => true,
                    'bookPublisher' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['bookAuthor']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookPublicationYear']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['bookTitle']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['bookPublisher']['evaluable']);
    }

    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}