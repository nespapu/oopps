<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\SectionRepository;
use PDO;

final class SqlSectionRepository implements SectionRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT
                orden AS `orden`,
                titulo AS `titulo` 
            FROM apartado 
            WHERE 
                codigo_oposicion = :codigoOposicion AND
                orden_tema = :ordenTema 
            ORDER BY numeracion ASC
        ";
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder
        ]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(
            static fn(array $row): array => [
                'sectionOrder' => (string) $row['orden'],
                'sectionTitle' => (string) $row['titulo'],
            ],
            $rows
        );
    }
}