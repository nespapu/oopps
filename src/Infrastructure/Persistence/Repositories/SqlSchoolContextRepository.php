<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\SchoolContextRepository;
use PDO;


final class SqlSchoolContextRepository implements SchoolContextRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    /**
     * @return array<int, array{
     *      schoolContextTeaching: string, 
     *      schoolContextCycle: string|null,
     *      schoolContextModule: string|null,
     *      schoolContextConcept: string,
     *      schoolContextApplication: string,
     *      schoolContextMethod: string|null
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT 
                ce.ensenyanza,
                ce.ciclo,
                ce.modulo,
                ce.concepto,
                ce.aplicacion,
                ce.metodo
            FROM contexto_escolar AS ce
            WHERE ce.codigo_oposicion = :codigoOposicion
                AND ce.orden_tema = :ordenTema
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'schoolContextTeaching' => (string) $row['ensenyanza'],
                'schoolContextCycle' => $row['ciclo'] !== null ? (string) $row['ciclo'] : null,
                'schoolContextModule' => $row['modulo'] !== null ? (string) $row['modulo'] : null,
                'schoolContextConcept' => (string) $row['concepto'],
                'schoolContextApplication' => (string) $row['aplicacion'],
                'schoolContextMethod' => $row['metodo'] !== null ? (string) $row['metodo'] : null
            ],
            $rows
        );
    }
}