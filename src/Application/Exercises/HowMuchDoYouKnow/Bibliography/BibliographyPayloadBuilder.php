<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Bibliography;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\BookRepository;

final class BibliographyPayloadBuilder
{

    public function __construct(
        private BookRepository $bookRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $fieldConfig = [
            'bookAuthor' => [
                'evaluable' => !$session->config()->isFlagEnabled('bookAuthor'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'bookPublicationYear' => [
                'evaluable' => !$session->config()->isFlagEnabled('bookPublicationYear'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],            
            'bookTitle' => [
                'evaluable' => !$session->config()->isFlagEnabled('bookTitle'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'bookPublisher' => [
                'evaluable' => !$session->config()->isFlagEnabled('bookPublisher'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ]
        ];

        $books = $this->bookRepository->findByTopic($oppositionCode, $topicOrder);

        return [
            StepPayloadKeys::STEP => ExerciseStep::BIBLIOGRAPHY->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $books,
                $difficulty,
                $fieldConfig
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $books,
                $fieldConfig
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
        array $books,
        Difficulty $difficulty,
        array $fieldConfig
    ): array {
        $items = [];

        foreach ($books as $index => $book) {
            $item = [
                'key' => 'book' . $index,
            ];

            foreach ($fieldConfig as $field => $config) {
                $item[$field] = [
                    'value' => $book[$field],
                    'hint' => $this->hintService->getHint(
                        $book[$field],
                        $difficulty,
                        $config['hintMode']
                    ),
                ];
            }

            $items[] = $item;
        }

        return $items;
    }

    private function buildExpected(
        array $books,
        array $fieldConfig
    ): array {
        $expected = [];

        foreach ($books as $index => $book) {
            $item = [
                'key' => 'book' . $index,
            ];

            foreach (array_keys($fieldConfig) as $field) {
                $item[$field] = $book[$field];
            }

            $expected[] = $item;
        }

        return $expected;
    }
}