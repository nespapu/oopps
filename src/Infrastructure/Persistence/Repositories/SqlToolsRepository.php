<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\ToolsRepository;
use PDO;

final class SqlToolsRepository implements ToolsRepository
{
    public function __construct(
        private readonly PDO $db
    ){}

    /**
     * @return array<int, array{toolName: string, toolDescription: string}>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT
                h.nombre,
                h.descripcion
            FROM tema_usar_herramienta AS tuh
            INNER JOIN herramienta h
                ON h.nombre = tuh.nombre_herramienta
            WHERE tuh.codigo_oposicion = :codigoOposicion
                AND tuh.orden_tema = :ordenTema
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'toolName' => (string) $row['nombre'],
                'toolDescription' => (string) $row['descripcion']
            ],
            $rows
        );
    }
}