<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface WebsiteRepository
{
    /**
     * @return array<int, array{
     *      websiteName: string, 
     *      websiteURL: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}