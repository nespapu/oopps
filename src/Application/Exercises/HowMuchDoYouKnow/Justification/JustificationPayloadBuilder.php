<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Justification;

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\JustificationRepository;

final class JustificationPayloadBuilder 
{
    public function __construct(
        private JustificationRepository $justificationRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();

        $justification = $this->justificationRepository->findByTopic($oppositionCode, $topicOrder);

        $difficulty = Difficulty::from($session->config()->difficulty());

        $cycleHintMode = HintMode::LETTERS;
        $lawHintMode = HintMode::LETTERS;
        $moduleHintMode = HintMode::LETTERS;

        return [
            StepPayloadKeys::STEP => ExerciseStep::JUSTIFICATION->value,

            StepPayloadKeys::META => [
                'topicOrder' => $topicOrder,
                'difficulty' => $difficulty,
                'flags' => $session->config()->flags(),
                'evaluable' => [
                    'cycles' => !$session->config()->isFlagEnabled('cycles'),
                    'laws' => !$session->config()->isFlagEnabled('laws'),
                    'modules' => !$session->config()->isFlagEnabled('modules')
                ],
                'hintModes' => [
                    'cycle' => HintMode::LETTERS,
                    'law' => HintMode::LETTERS,
                    'module' => HintMode::LETTERS
                ]
            ],

            StepPayloadKeys::ITEMS => $this->buildItems(
                $justification,
                $difficulty,
                $cycleHintMode,
                $lawHintMode,
                $moduleHintMode
            ),

            StepPayloadKeys::EXPECTED => $this->buildExpectedItems($justification)
        ];
    }

    private function buildItems(
        array $justification,
        Difficulty $difficulty,
        HintMode $cycleHintMode,
        HintMode $lawHintMode,
        HintMode $moduleHintMode
    ): array 
    {
        $items = [];

        foreach($justification as $index => $cycle){
            $item = [
                'key' => 'cycle'.$index,
                'name' => $cycle['cycleName'],
                'hint' => $this->hintService->getHint($cycle['cycleName'], $difficulty, $cycleHintMode),
                'laws' => [],
                'modules' => []
            ];

            foreach ($cycle['laws'] as $index2 => $law){
                $item['laws'][] = [
                    'key' => 'law'.$index2,
                    'name' => $law,
                    'hint' => $this->hintService->getHint($law, $difficulty, $lawHintMode)
                ];
            }

            foreach ($cycle['modules'] as $index2 => $module){
                $item['modules'][] = [
                    'key' => 'module'.$index2,
                    'name' => $module,
                    'hint' => $this->hintService->getHint($module, $difficulty, $moduleHintMode)
                ];
            }

            $items[] = $item;
        }

        return $items;
    }

    private function buildExpectedItems(array $justification): array
    {
        $items = [];
        
        foreach($justification as $index => $cycle){
            $item = [
                'key' => 'cycle'.$index,
                'name' => $cycle['cycleName'],
                'laws' => [],
                'modules' => []
            ];
            
            foreach ($cycle['laws'] as $index2 => $law){
                $item['laws'][] = [
                    'key' => 'law'.$index2,
                    'name' => $law
                ];
            }
            
            foreach ($cycle['modules'] as $index2 => $module){
                $item['modules'][] = [
                    'key' => 'module'.$index2,
                    'name' => $module
                ];
            }
            $items[] = $item;
        }

        return $items;
    }
}