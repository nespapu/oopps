<?php

namespace App\Application\Exercises\StepBuilder;

use App\Application\Exercises\Payload\StepPayloadKeys;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\TopicRepository;

final class HowMuchDoYouKnowTitlePayloadBuilder
{
    public function __construct(
        private TopicRepository $topicRepository,
        private HintService $hintService
    ) {}

    public function build(ExerciseSession $session): array
    {
        $oppositionCode = $session->userContext()->oppositionCode();
        $topicOrder = $session->config()->topicId();

        $title = $this->topicRepository->findTitleByOppositionCodeAndOrder($oppositionCode, $topicOrder);

        $difficulty = Difficulty::from($session->config()->difficulty());
        $hintMode = HintMode::WORDS;

        $hint = $title === null
            ? '(no hint generated)'
            : $this->hintService->getHint($title, $difficulty, $hintMode);

        return [
            'step' => ExerciseStep::TITLE->value,
            StepPayloadKeys::ITEMS => [
                [
                    'key' => 'titulo',
                    'tipo' => 'text',
                    'nombre' => 'titulo',
                    'pista' => $hint,
                    'placeholder' => 'Escribe el título del tema',
                ],
            ],
            StepPayloadKeys::META => [
                'numeracionTema' => $topicOrder,
                'tituloTema' => $title,
                'gradoDificultad' => $session->config()->difficulty(),
                'banderas' => $session->config()->flags(),
                'tipoPista' => $hintMode->value,
            ],
            'expected' => [
                'titulo' => $title,
            ],
        ];
    }
}