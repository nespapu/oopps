<?php


declare(strict_types=1);


namespace App\Domain\Temas;


interface JustificationRepository
{
    /**
    * @return array<int, array{
    *     cycleName: string,
    *     laws: array<int, string>,
    *     modules: array<int, string>
    * }>
    */
    public function findByTopic(string $oppositionCode, int $topicOrder): array;
}