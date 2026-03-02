<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\TopicRepository;
use PDO;

final class TopicRepositorySQL implements TopicRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    public function findByOppositionCode(string $oppositionCode): array
    {
        $sql = "
            SELECT
                numeracion AS `numeracion`,
                titulo AS `titulo` 
            FROM tema 
            WHERE codigo_oposicion = :codigoOposicion 
            ORDER BY numeracion ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['codigoOposicion' => $oppositionCode]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'numeracion' => (int) $row['numeracion'],
                'titulo' => (string) $row['titulo'],
            ],
            $rows
        );
    }

    public function findTitleByOppositionCodeAndOrder(string $oppositionCode, int $order): ?string
    {
        $sql = "
            SELECT titulo AS `titulo`
            FROM tema
            WHERE codigo_oposicion = :codigoOposicion
              AND orden = :orden
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'orden' => $order,
        ]);

        $title = $statement->fetchColumn();

        if ($title === false) {
            return null;
        }

        return (string) $title;
    }

    public function findRandomOrderByOppositionCode(string $oppositionCode): ?int
    {
        $sql = "
            SELECT orden AS `orden`
            FROM tema
            WHERE codigo_oposicion = :codigoOposicion
            ORDER BY RAND()
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['codigoOposicion' => $oppositionCode]);

        $order = $statement->fetchColumn();

        if ($order === false) {
            return null;
        }

        return (int) $order;
    }
}