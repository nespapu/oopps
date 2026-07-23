<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\QuotesRepository;

final class FakeQuotesRepository implements QuotesRepository
{
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return [
            [
                'quoteConcept' => 'Concept A',
                'quoteAuthor' => 'Author A',
                'quoteYear' => 'Year 1',
                'quoteContent' => 'Content A',
                'quoteSectionOrder' => '1',
                'quoteSectionTitle' => 'Title 1',
            ],
            [
                'quoteConcept' => 'Concept B',
                'quoteAuthor' => 'Author B',
                'quoteYear' => 'Year 2',
                'quoteContent' => 'Content B',
                'quoteSectionOrder' => '2',
                'quoteSectionTitle' => 'Title 2',
            ],
        ];
    }
}