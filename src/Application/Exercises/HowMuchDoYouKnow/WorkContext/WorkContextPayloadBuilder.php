<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\WorkContext;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\WorkContextRepository;

final class WorkContextPayloadBuilder
{
    public function __construct(
        private WorkContextRepository $workContextRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $fieldConfig = [
            'workContextField' => [
                'evaluable' => !$session->config()->isFlagEnabled('workContextField'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'workContextRole' => [
                'evaluable' => !$session->config()->isFlagEnabled('schoolContextRole'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],            
            'workContextConcept' => [
                'evaluable' => !$session->config()->isFlagEnabled('workContextConcept'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'workContextApplication' => [
                'evaluable' => !$session->config()->isFlagEnabled('workContextApplication'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'hintMode' => HintMode::WORDS,
                'threshold' => 0.8
            ],
            'workContextBenefit' => [
                'evaluable' => !$session->config()->isFlagEnabled('workContextBenefit'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'hintMode' => HintMode::WORDS,
                'threshold' => 0.8
            ],
        ];

        $workContexts = $this->workContextRepository->findByTopic($oppositionCode, $topicOrder);

        return [
            StepPayloadKeys::STEP => ExerciseStep::WORK_CONTEXT->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $workContexts,
                $difficulty,
                $fieldConfig
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $workContexts,
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
        array $workContexts,
        Difficulty $difficulty,
        array $fieldConfig
    ): array {
        $items = [];

        foreach ($workContexts as $index => $workContext) {
            $item = [
                'key' => 'workContext' . $index,
            ];

            foreach ($fieldConfig as $field => $config) {
                if ($workContext[$field] === null) {
                    continue;
                }

                $item[$field] = [
                    'value' => $workContext[$field],
                    'hint' => $this->hintService->getHint(
                        $workContext[$field],
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
        array $workContexts,
        array $fieldConfig
    ): array {
        $expected = [];

        foreach ($workContexts as $index => $workContext) {
            $item = [
                'key' => 'workContext' . $index,
            ];

            foreach (array_keys($fieldConfig) as $field) {
                if ($workContext[$field] === null) {
                    continue;
                }

                $item[$field] = $workContext[$field];
            }

            $expected[] = $item;
        }

        return $expected;
    }
}