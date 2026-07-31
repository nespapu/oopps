<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface SchoolContextRepository
{
    /**
     * @return array<int, array{
     *      schoolContextTeaching: string, 
     *      schoolContextCycle: string|null,
     *      schoolContextModule: string|null,
     *      schoolContextConcept: string,
     *      schoolContextApplication: string,
     *      schoolContextMethod: string|null
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}