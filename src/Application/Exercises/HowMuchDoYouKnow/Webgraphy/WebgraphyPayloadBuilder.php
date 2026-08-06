<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Webgraphy;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\WebsiteRepository;

final class WebgraphyPayloadBuilder
{

    public function __construct(
        private WebsiteRepository $websiteRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $fieldConfig = [
            'websiteName' => [
                'evaluable' => !$session->config()->isFlagEnabled('websiteName'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ],
            'websiteURL' => [
                'evaluable' => !$session->config()->isFlagEnabled('websiteURL'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => HintMode::LETTERS
            ]
        ];

        $websites = $this->websiteRepository->findByTopic($oppositionCode, $topicOrder);

        return [
            StepPayloadKeys::STEP => ExerciseStep::WEBGRAPHY->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $websites,
                $difficulty,
                $fieldConfig
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $websites,
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
        array $websites,
        Difficulty $difficulty,
        array $fieldConfig
    ): array {
        $items = [];

        foreach ($websites as $index => $website) {
            $item = [
                'key' => 'website' . $index,
            ];

            foreach ($fieldConfig as $field => $config) {
                $item[$field] = [
                    'value' => $website[$field],
                    'hint' => $this->hintService->getHint(
                        $website[$field],
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
        array $websites,
        array $fieldConfig
    ): array {
        $expected = [];

        foreach ($websites as $index => $website) {
            $item = [
                'key' => 'website' . $index,
            ];

            foreach (array_keys($fieldConfig) as $field) {
                $item[$field] = $website[$field];
            }

            $expected[] = $item;
        }

        return $expected;
    }
}