<?php

declare(strict_types=1);

namespace Tests\Application\Exercises\HowMuchDoYouKnow\PayloadBuilder;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Quotes\QuotesPayloadBuilder;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Exercise\FakeQuotesRepository;

final class QuotesPayloadBuilderTest extends TestCase
{
    public function testBuildsPayloadWithQuotesExpectedValuesAndMetadata(): void
    {
        $builder = new QuotesPayloadBuilder(new FakeQuotesRepository(), new HintService());

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

        $this->assertSame(ExerciseStep::QUOTES->value, $payload[StepPayloadKeys::STEP]);
        $this->assertCount(2, $payload[StepPayloadKeys::ITEMS]);
        $this->assertSame('quote0', $payload[StepPayloadKeys::ITEMS][0]['key']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::ITEMS][0]['quoteConcept']['value']);
        $this->assertSame('Author A', $payload[StepPayloadKeys::ITEMS][0]['quoteAuthor']['value']);
        $this->assertSame('Year 1', $payload[StepPayloadKeys::ITEMS][0]['quoteYear']['value']);
        $this->assertSame('Content A', $payload[StepPayloadKeys::ITEMS][0]['quoteContent']['value']);
        $this->assertSame('1', $payload[StepPayloadKeys::ITEMS][0]['quoteSectionOrder']['value']);
        $this->assertSame('Title 1', $payload[StepPayloadKeys::ITEMS][0]['quoteSectionTitle']['value']);
        $this->assertSame('quote1', $payload[StepPayloadKeys::ITEMS][1]['key']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::ITEMS][1]['quoteConcept']['value']);
        $this->assertSame('Author B', $payload[StepPayloadKeys::ITEMS][1]['quoteAuthor']['value']);
        $this->assertSame('Year 2', $payload[StepPayloadKeys::ITEMS][1]['quoteYear']['value']);
        $this->assertSame('Content B', $payload[StepPayloadKeys::ITEMS][1]['quoteContent']['value']);
        $this->assertSame('2', $payload[StepPayloadKeys::ITEMS][1]['quoteSectionOrder']['value']);
        $this->assertSame('Title 2', $payload[StepPayloadKeys::ITEMS][1]['quoteSectionTitle']['value']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['topicOrder']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteConcept']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteConcept']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteConcept']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteAuthor']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteAuthor']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteAuthor']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteYear']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteYear']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteYear']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteContent']['evaluable']);
        $this->assertSame(EvaluationMode::SIMILARITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteContent']['evaluationMode']);
        $this->assertSame(0.8, $payload[StepPayloadKeys::META]['fieldConfig']['quoteContent']['threshold']);
        $this->assertSame(HintMode::WORDS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteContent']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionOrder']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionOrder']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionOrder']['hintMode']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionTitle']['evaluable']);
        $this->assertSame(EvaluationMode::EQUALITY, $payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionTitle']['evaluationMode']);
        $this->assertSame(HintMode::LETTERS, $payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionTitle']['hintMode']);

        $this->assertSame('quote0', $payload[StepPayloadKeys::EXPECTED][0]['key']);
        $this->assertSame('Concept A', $payload[StepPayloadKeys::EXPECTED][0]['quoteConcept']);
        $this->assertSame('Author A', $payload[StepPayloadKeys::EXPECTED][0]['quoteAuthor']);
        $this->assertSame('Year 1', $payload[StepPayloadKeys::EXPECTED][0]['quoteYear']);
        $this->assertSame('Content A', $payload[StepPayloadKeys::EXPECTED][0]['quoteContent']);
        $this->assertSame('1', $payload[StepPayloadKeys::EXPECTED][0]['quoteSectionOrder']);
        $this->assertSame('Title 1', $payload[StepPayloadKeys::EXPECTED][0]['quoteSectionTitle']);
        $this->assertSame('quote1', $payload[StepPayloadKeys::EXPECTED][1]['key']);
        $this->assertSame('Concept B', $payload[StepPayloadKeys::EXPECTED][1]['quoteConcept']);
        $this->assertSame('Author B', $payload[StepPayloadKeys::EXPECTED][1]['quoteAuthor']);
        $this->assertSame('Year 2', $payload[StepPayloadKeys::EXPECTED][1]['quoteYear']);
        $this->assertSame('Content B', $payload[StepPayloadKeys::EXPECTED][1]['quoteContent']);
        $this->assertSame('2', $payload[StepPayloadKeys::EXPECTED][1]['quoteSectionOrder']);
        $this->assertSame('Title 2', $payload[StepPayloadKeys::EXPECTED][1]['quoteSectionTitle']);
    }


    public function testMarksCheckedFieldsAsNotEvaluableAndUncheckedFieldsAsEvaluable(): void
    {
        $builder = new QuotesPayloadBuilder(new FakeQuotesRepository(), new HintService());


        $session = ExerciseSession::start(
            exerciseType: ExerciseType::howMuchDoYouKnowTopic(),
            userContext: $this->userContextDummy(),
            config: new ExerciseConfig(
                topicId: 16,
                difficulty: 2,
                flags: [
                    'quoteConcept' => true,
                    'quoteAuthor' => false,
                    'quoteYear' => true,
                    'quoteContent' => true,
                    'quoteSectionOrder' => false,
                    'quoteSectionTitle' => false
                ]
            ),
            firstStep: ExerciseStep::first()
        );

        $payload = $builder->build($session);

        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['quoteConcept']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteAuthor']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['quoteYear']['evaluable']);
        $this->assertFalse($payload[StepPayloadKeys::META]['fieldConfig']['quoteContent']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionOrder']['evaluable']);
        $this->assertTrue($payload[StepPayloadKeys::META]['fieldConfig']['quoteSectionTitle']['evaluable']);
    }


    private function userContextDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}