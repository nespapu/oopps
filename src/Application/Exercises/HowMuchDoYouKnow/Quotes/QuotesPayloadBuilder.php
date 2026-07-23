<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Quotes;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\QuotesRepository;

final class QuotesPayloadBuilder
{
    public function __construct(
        private QuotesRepository $quotesRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $quoteConceptHintMode = HintMode::LETTERS;
        $quoteAuthorHintMode = HintMode::LETTERS;
        $quoteYearHintMode = HintMode::LETTERS;
        $quoteContentHintMode = HintMode::WORDS;
        $quoteSectionOrderHintMode = HintMode::LETTERS;
        $quoteSectionTitleHintMode = HintMode::LETTERS;
        $fieldConfig = [
            'quoteConcept' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteConcept'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $quoteConceptHintMode
            ],
            'quoteAuthor' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteAuthor'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $quoteAuthorHintMode
            ],
            'quoteYear' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteYear'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $quoteYearHintMode
            ],
            'quoteContent' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteContent'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'threshold' => 0.8,
                'hintMode' => $quoteContentHintMode
            ],
            'quoteSectionOrder' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteSectionOrder'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $quoteSectionOrderHintMode
            ],
            'quoteSectionTitle' => [
                'evaluable' => !$session->config()->isFlagEnabled('quoteSectionTitle'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $quoteSectionTitleHintMode
            ],
        ];

        $quotes = $this->quotesRepository->findByTopic($oppositionCode, $topicOrder);
        
        return [
            StepPayloadKeys::STEP => ExerciseStep::QUOTES->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $quotes,
                $difficulty,
                $quoteConceptHintMode,
                $quoteAuthorHintMode,
                $quoteYearHintMode,
                $quoteContentHintMode,
                $quoteSectionOrderHintMode,
                $quoteSectionTitleHintMode
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $quotes
            )
        ];
    }

    private function buildMeta(
        int $topicOrder,
        Difficulty $difficulty,
        array $flags,
        array $fieldConfig
    ): array
    {
        return [
            'topicOrder' => $topicOrder,
            'difficulty' => $difficulty->value,
            'flags' => $flags,
            'fieldConfig' => $fieldConfig
        ];
    }

    private function buildItems(
        array $quotes,
        Difficulty $difficulty,
        HintMode $quoteConceptHintMode,
        HintMode $quoteAuthorHintMode,
        HintMode $quoteYearHintMode,
        HintMode $quoteContentHintMode,
        HintMode $quoteSectionOrderHintMode,
        HintMode $quoteSectionTitleHintMode
    ): array
    {
        return array_map(
            fn(array $quote, int $index): array => [
                'key' => 'quote'.$index,
                'quoteConcept' => [
                    'value' => $quote['quoteConcept'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteConcept'],
                        $difficulty,
                        $quoteConceptHintMode
                    )
                ],
                'quoteAuthor' => [
                    'value' => $quote['quoteAuthor'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteAuthor'],
                        $difficulty,
                        $quoteAuthorHintMode
                    )
                ],
                'quoteYear' => [
                    'value' => $quote['quoteYear'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteYear'],
                        $difficulty,
                        $quoteYearHintMode
                    )
                ],
                'quoteContent' => [
                    'value' => $quote['quoteContent'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteContent'],
                        $difficulty,
                        $quoteContentHintMode
                    )
                ],
                'quoteSectionOrder' => [
                    'value' => $quote['quoteSectionOrder'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteSectionOrder'],
                        $difficulty,
                        $quoteSectionOrderHintMode
                    )
                ],
                'quoteSectionTitle' => [
                    'value' => $quote['quoteSectionTitle'],
                    'hint' => $this->hintService->getHint(
                        $quote['quoteSectionTitle'],
                        $difficulty,
                        $quoteSectionTitleHintMode
                    )
                ],
            ],
            $quotes,
            array_keys($quotes)
        );
    }

    private function buildExpected(
        array $quotes
    ): array
    {
        return array_map(
            static fn(array $quote, int $index): array => [
                'key' => 'quote'.$index,
                'quoteConcept' => $quote['quoteConcept'],
                'quoteAuthor' => $quote['quoteAuthor'],
                'quoteYear' => $quote['quoteYear'],
                'quoteContent' => $quote['quoteContent'],
                'quoteSectionOrder' => $quote['quoteSectionOrder'],
                'quoteSectionTitle' => $quote['quoteSectionTitle']
            ],
            $quotes,
            array_keys($quotes)
        );
    }
}