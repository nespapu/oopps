<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\WorkContextRepository;
use PDO;

final class SqlWorkContextRepository implements WorkContextRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    /**
     * @return array<int, array{
     *      workContextField: string, 
     *      workContextRole: string|null,
     *      workContextConcept: string,
     *      workContextApplication: string,
     *      workContextBenefit: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT 
                cl.campo,
                cl.profesional,
                cl.concepto,
                cl.tarea,
                cl.beneficio
            FROM contexto_laboral AS cl
            WHERE cl.codigo_oposicion = :codigoOposicion
                AND cl.orden_tema = :ordenTema
        ";


        $statement = $this->db->prepare($sql);


        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);


        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


        return array_map(
            static fn(array $row): array => [
                'workContextField' => (string) $row['campo'],
                'workContextRole' => $row['profesional'] !== null ? (string) $row['profesional'] : null,
                'workContextConcept' => (string) $row['concepto'],
                'workContextApplication' => (string) $row['tarea'],
                'workContextBenefit' => (string) $row['beneficio']
            ],
            $rows
        );
    }
}