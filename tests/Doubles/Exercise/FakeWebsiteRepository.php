<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\WebsiteRepository;

final class FakeWebsiteRepository implements WebsiteRepository
{
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return [
            [
                'websiteName' => 'Website A',
                'websiteURL' => 'URL A',
            ],
            [
                'websiteName' => 'Website B',
                'websiteURL' => 'URL B',
            ],
        ];
    }
}