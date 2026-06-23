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
        $sectionTitleHintMode = HintMode::LETTERS;


        return [
            StepPayloadKeys::STEP => ExerciseStep::INDEX->value,

            StepPayloadKeys::ITEMS => $this->buildSectionItems(
                $sections,
                $difficulty,
                $sectionOrderHintMode,
                $sectionTitleHintMode
            ),

            StepPayloadKeys::META => [
                    'topicOrder' => $topicOrder,
                    'difficulty' => $difficulty->value,
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

            StepPayloadKeys::EXPECTED => $this->buildExpectedItems($sections),
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
                'sectionOrder' => $row['sectionOrder'],
                'sectionTitle' => $row['sectionTitle'],
                'hints' => [
                    'sectionOrder' => '',  // TODO: Define hint strategy for structured fields (e.g. section numbering).
                    'sectionTitle' => $this->hintService->getHint(
                        $row['sectionTitle'],
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
                'sectionOrder' => $row['sectionOrder'],
                'sectionTitle' => $row['sectionTitle'],
            ],
            $sections,
            array_keys($sections)
        );
    }
}