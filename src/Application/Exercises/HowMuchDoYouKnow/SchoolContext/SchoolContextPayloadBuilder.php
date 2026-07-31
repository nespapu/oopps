<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\SchoolContext;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\SchoolContextRepository;

final class SchoolContextPayloadBuilder
{

    public function __construct(
        private SchoolContextRepository $schoolContextRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $fieldConfig = [
            'schoolContextTeaching' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextTeaching'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'schoolContextCycle' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextCycle'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],            
            'schoolContextModule' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextModule'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'schoolContextConcept' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextConcept'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'schoolContextApplication' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextApplication'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'hintMode' => HintMode::WORDS,
                'threshold' => 0.8
            ],
            'schoolContextMethod' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextMethod'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'hintMode' => HintMode::WORDS,
                'threshold' => 0.8
            ],
        ];

        $schoolContexts = $this->schoolContextRepository->findByTopic($oppositionCode, $topicOrder);

        return [
            StepPayloadKeys::STEP => ExerciseStep::SCHOOL_CONTEXT->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $schoolContexts,
                $difficulty,
                $fieldConfig
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $schoolContexts,
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
        array $schoolContexts,
        Difficulty $difficulty,
        array $fieldConfig
    ): array {
        $items = [];

        foreach ($schoolContexts as $index => $schoolContext) {
            $item = [
                'key' => 'schoolContext' . $index,
            ];

            foreach ($fieldConfig as $field => $config) {
                if ($schoolContext[$field] === null) {
                    continue;
                }

                $item[$field] = [
                    'value' => $schoolContext[$field],
                    'hint' => $this->hintService->getHint(
                        $schoolContext[$field],
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
        array $schoolContexts,
        array $fieldConfig
    ): array {
        $expected = [];

        foreach ($schoolContexts as $index => $schoolContext) {
            $item = [
                'key' => 'schoolContext' . $index,
            ];

            foreach (array_keys($fieldConfig) as $field) {
                if ($schoolContext[$field] === null) {
                    continue;
                }

                $item[$field] = $schoolContext[$field];
            }

            $expected[] = $item;
        }

        return $expected;
    }

}