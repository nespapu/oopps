<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface WorkContextRepository
{
    /**
     * @return array<int, array{
     *      workContextField: string, 
     *      workContextRole: string|null,
     *      workContextConcept: string,
     *      workContextApplication: string,
     *      workContextBenefit: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}