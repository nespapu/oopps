<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface SectionRepository
{
    /**
     * @return array<int, array{sectionOrder: string, sectionTitle: string}>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}

