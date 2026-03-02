<?php

declare(strict_types=1);

namespace tests\Domain\Exercise\Doubles;

use App\Domain\Temas\TopicRepository;

final class FakeTopicRepository implements TopicRepository
{
    public function __construct(
        private ?string $title,
        private ?int $order
    ) {}

    public function findByOppositionCode(string $oppositionCode): array
    {
        return [
            ['numeracion' => 16, 'titulo' => 'Sistemas operativos. Gestión de procesos'],
        ];
    }

    public function findTitleByOppositionCodeAndOrder(string $oppositionCode, int $order): ?string
    {
        return $this->title;
    }

    public function findRandomOrderByOppositionCode(string $oppositionCode): ?int
    {
        return $this->order;
    }
}