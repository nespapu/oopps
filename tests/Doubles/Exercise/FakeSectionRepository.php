<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\SectionRepository;

final class FakeSectionRepository implements SectionRepository
{
    public function __construct(
        private array $sections
    ) {}

    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return $this->sections;
    }
}