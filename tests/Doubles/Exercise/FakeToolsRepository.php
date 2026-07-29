<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\ToolsRepository;

final class FakeToolsRepository implements ToolsRepository
{
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return [
            [
                'toolName' => 'Tool A',
                'toolDescription' => 'Description tool A',
            ],
            [
                'toolName' => 'Tool B',
                'toolDescription' => 'Description tool B',
            ],
        ];
    }
}