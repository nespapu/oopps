<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface TopicRepository
{
    public function findByOppositionCode(string $oppositionCode): array;

    public function findTitleByOppositionCodeAndOrder(string $oppositionCode, int $order): ?string;

    public function findRandomOrderByOppositionCode(string $oppositionCode): ?int;
}
