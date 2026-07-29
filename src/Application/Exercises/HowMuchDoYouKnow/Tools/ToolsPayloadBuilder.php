<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Tools;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\ToolsRepository;

final class ToolsPayloadBuilder
{
    public function __construct(
        private ToolsRepository $toolsRepository,
        private HintService $hintService
    ){}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();
        $difficulty = Difficulty::from($session->config()->difficulty());
        $flags = $session->config()->flags();
        $toolNameHintMode = HintMode::LETTERS;
        $toolDescriptionHintMode = HintMode::WORDS;
        $fieldConfig = [
            'toolName' => [
                'evaluable' => !$session->config()->isFlagEnabled('toolName'),
                'evaluationMode' => EvaluationMode::EQUALITY,
                'hintMode' => $toolNameHintMode
            ],
            'toolDescription' => [
                'evaluable' => !$session->config()->isFlagEnabled('toolDescription'),
                'evaluationMode' => EvaluationMode::SIMILARITY,
                'hintMode' => $toolDescriptionHintMode,
                'threshold' => 0.8                
            ]
        ];

        $tools = $this->toolsRepository->findByTopic($oppositionCode, $topicOrder);

        return [
            StepPayloadKeys::STEP => ExerciseStep::TOOLS->value,

            StepPayloadKeys::META => $this->buildMeta(
                $topicOrder,
                $difficulty,
                $flags,
                $fieldConfig
            ),

            StepPayloadKeys::ITEMS => $this->buildItems(
                $tools,
                $difficulty,
                $toolNameHintMode,
                $toolDescriptionHintMode
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpected(
                $tools
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
        array $tools,
        Difficulty $difficulty,
        HintMode $toolNameHintMode,
        HintMode $toolDescriptionHintMode
    ): array
    {
        return array_map(
            fn(array $tool, int $index): array => [
                'key' => 'tool'.$index,
                'toolName' => [
                    'value' => $tool['toolName'],
                    'hint' => $this->hintService->getHint(
                        $tool['toolName'],
                        $difficulty,
                        $toolNameHintMode
                    )
                ],
                'toolDescription' => [
                    'value' => $tool['toolDescription'],
                    'hint' => $this->hintService->getHint(
                        $tool['toolDescription'],
                        $difficulty,
                        $toolDescriptionHintMode
                    )
                ],                
            ],
            $tools,
            array_keys($tools)
        );
    }

    private function buildExpected(
        array $tools
    ): array
    {
        return array_map(
            static fn(array $tool, int $index): array => [
                'key' => 'tool'.$index,
                'toolName' => $tool['toolName'],
                'toolDescription' => $tool['toolDescription']
            ],
            $tools,
            array_keys($tools)
        );
    }
}