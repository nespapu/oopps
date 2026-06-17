<?php


namespace App\Application\Exercises\HowMuchDoYouKnow\Index;


use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\SectionRepository;

final class IndexPayloadBuilder
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private HintService $hintService
    ) {}


    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();

        $sections = $this->sectionRepository->findByTopic($oppositionCode, $topicOrder);

        $difficulty = Difficulty::from($session->config()->difficulty());
        $sectionOrderHintMode = HintMode::LETTERS;
        $sectionTitleHintMode = HintMode::WORDS;


        return [
            'step' => ExerciseStep::INDEX->value,

            StepPayloadKeys::ITEMS => $this->buildSectionItems(
                $sections,
                $difficulty,
                $sectionOrderHintMode,
                $sectionTitleHintMode
            ),

            StepPayloadKeys::META => [
                    'topicOrder' => $topicOrder,
                    'difficulty' => $session->config()->difficulty(),
                    'flags' => $session->config()->flags(),
                    'evaluable' => [
                        'sectionOrder' => !$session->config()->isFlagEnabled('sectionOrder'),
                        'sectionTitle' => !$session->config()->isFlagEnabled('sectionTitle'),
                    ],
                    'hintModes' => [
                        'sectionOrder' => 'letters',
                        'sectionTitle' => 'words',
                    ]
            ],

            'expected' => $this->buildExpectedItems($sections),
        ];
    }

    private function buildSectionItems(
        array $sections,
        Difficulty $difficulty,
        HintMode $sectionOrderHintMode,
        HintMode $sectionTitleHintMode
    ): array {
        return array_map(
            fn(array $row, int $index): array => [
                'key' => 'item' . $index,
                'sectionOrder' => $row['orden'],
                'sectionTitle' => $row['titulo'],
                'hints' => [
                    'sectionOrder' => '',  // TODO: Define hint strategy for structured fields (e.g. section numbering).
                    'sectionTitle' => $this->hintService->getHint(
                        $row['titulo'],
                        $difficulty,
                        $sectionTitleHintMode
                    ),
                ],
            ],
            $sections,
            array_keys($sections)
        );
    }

    private function buildExpectedItems(array $sections): array
    {
        return array_map(
            static fn(array $row, int $index): array => [
                'key' => 'item' . $index,
                'sectionOrder' => $row['orden'],
                'sectionTitle' => $row['titulo'],
            ],
            $sections,
            array_keys($sections)
        );
    }
}