<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Title;

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\TopicRepository;

final class TitlePayloadBuilder
{
    public function __construct(
        private TopicRepository $topicRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();

        $title = $this->topicRepository->findTitleByOppositionCodeAndOrder(
            $oppositionCode,
            $topicOrder
        );

        $difficulty = Difficulty::from($session->config()->difficulty());
        $hintMode = HintMode::WORDS;

        $hint = $title === null
            ? '(no hint generated)'
            : $this->hintService->getHint($title, $difficulty, $hintMode);

        return [
            'step' => ExerciseStep::TITLE->value,

            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'name' => 'title',
                    'hint' => $hint,
                    'placeholder' => 'Escribe el título del tema',
                ],
            ],

            StepPayloadKeys::META => [
                    'topicOrder' => $topicOrder,
                    'topicTitle' => $title,
                    'difficulty' => $session->config()->difficulty(),
                    'flags' => $session->config()->flags(),
                    'hintType' => $hintMode->value,
            ],

            'expected' => [
                'title' => $title,
            ],
        ];
    }
}