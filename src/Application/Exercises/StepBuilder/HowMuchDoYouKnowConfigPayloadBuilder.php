<?php

namespace App\Application\Exercises\StepBuilder;

use App\Domain\Auth\UserContext;
use App\Domain\Exercise\Difficulty;
use App\Domain\Temas\TopicRepository;

final class HowMuchDoYouKnowConfigPayloadBuilder
{
    public function __construct(
        private TopicRepository $topicRepository
    ) {}

    public function build(UserContext $ctx): array
    {
        return [
            'temas' => $this->buildTopicOptions($ctx),
            'gradosDificultad' => $this->buildDifficultyOptions(),
            'defecto' => [
                'tema' => 0,
                'gradoDificultad' => Difficulty::MEDIUM->value,
            ],
        ];
    }

    private function buildTopicOptions(UserContext $ctx): array
    {
        $topics = $this->topicRepository->findByOppositionCode($ctx->oppositionCode());

        $options = array_map(
            fn(array $row) => [
                'valor' => (int) $row['numeracion'],
                'etiqueta' => $row['titulo'],
            ],
            $topics
        );

        // Add the "random" option
        array_unshift($options, [
            'valor' => 0,
            'etiqueta' => 'Aleatorio',
        ]);

        return $options;
    }

    private function buildDifficultyOptions(): array
    {
        return array_map(
            static fn(Difficulty $difficulty) => [
                'valor' => $difficulty->value,
                'etiqueta' => $difficulty->label(),
            ],
            Difficulty::cases()
        );
    }
}
