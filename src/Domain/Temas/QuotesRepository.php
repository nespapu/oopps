<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface QuotesRepository
{
    /**
     * @return array<int, array{
     *      quoteConcept: string, 
     *      quoteAuthor: string,
     *      quoteYear: string,
     *      quoteContent: string,
     *      quoteSectionOrder: string,
     *      quoteSectionTitle: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}

