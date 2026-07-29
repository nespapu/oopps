<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface ToolsRepository
{
    /**
     * @return array<int, array{toolName: string, toolDescription: string}>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}