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
            'topics' => $this->buildTopicOptions($ctx),
            'difficultyLevels' => $this->buildDifficultyOptions(),
            'defaults' => [
                'topicOrder' => 0,
                'difficulty' => Difficulty::MEDIUM->value,
            ],
        ];
    }

    private function buildTopicOptions(UserContext $ctx): array
    {
        $topics = $this->topicRepository->findByOppositionCode($ctx->oppositionCode());

        $options = array_map(
            static fn(array $row) => [
                'value' => (int) $row['numeracion'],
                'label' => (string) $row['titulo'],
            ],
            $topics
        );

        array_unshift($options, [
            'value' => 0,
            'label' => 'Aleatorio',
        ]);

        return $options;
    }

    private function buildDifficultyOptions(): array
    {
        return array_map(
            static fn(Difficulty $difficulty) => [
                'value' => $difficulty->value,
                'label' => $difficulty->label(), // ojo: que label() esté en español (o lo traducimos)
            ],
            Difficulty::cases()
        );
    }
}