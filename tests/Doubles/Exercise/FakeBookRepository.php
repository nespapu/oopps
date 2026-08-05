<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\BookRepository;

final class FakeBookRepository implements BookRepository
{
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return [
            [
                'bookAuthor' => 'Author A',
                'bookPublicationYear' => 'Year A',
                'bookTitle' => 'Title A',
                'bookPublisher' => 'Publisher A',
            ],
            [
                'bookAuthor' => 'Author B',
                'bookPublicationYear' => 'Year B',
                'bookTitle' => 'Title B',
                'bookPublisher' => 'Publisher B',
            ],
        ];
    }
}