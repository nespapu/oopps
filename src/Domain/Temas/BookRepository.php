<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface BookRepository
{
    /**
     * @return array<int, array{
     *      bookTitle: string, 
     *      bookAuthor: string,
     *      bookPublisher: string,
     *      bookPublicationYear: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}