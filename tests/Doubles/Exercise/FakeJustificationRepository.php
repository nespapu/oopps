<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\JustificationRepository;

final class FakeJustificationRepository implements JustificationRepository
{
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return [
            [
                'cycleName' => 'Cycle A',
                'laws' => [
                    'Law A1',
                    'Law A2',
                ],
                'modules' => [
                    'Module A1',
                ],
            ],
            [
                'cycleName' => 'Cycle B',
                'laws' => [
                    'Law B1',
                ],
                'modules' => [
                    'Module B1',
                    'Module B2',
                ],
            ],
        ];
    }
}