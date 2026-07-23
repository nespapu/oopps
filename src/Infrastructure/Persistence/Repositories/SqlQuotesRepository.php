<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\QuotesRepository;
use PDO;

final class SqlQuotesRepository implements QuotesRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}


    /**
     * @return array<int, array{
     *      quoteConcept: string, 
     *      quoteAuthor: string,
     *      quoteYear: string,
     *      quoteContent: string,
     *      quoteSectionOrder: string,
     *      quoteSectionTitle: string
     * }>
     */    
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT 
                atc.concepto_cita,
                atc.autor_cita,
                atc.anyo_cita,
                a.orden,
                a.titulo,
                c.contenido
            FROM apartado_tener_cita AS atc
            INNER JOIN cita AS c
                ON c.concepto = atc.concepto_cita
                AND c.autor = atc.autor_cita
                AND c.anyo = atc.anyo_cita
            INNER JOIN apartado AS a
                ON a.codigo_oposicion = atc.codigo_oposicion
                AND a.orden_tema = atc.orden_tema
                AND a.orden = atc.orden_apartado
            WHERE atc.codigo_oposicion = :codigoOposicion
                AND atc.orden_tema = :ordenTema
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'quoteConcept' => (string) $row['concepto_cita'],
                'quoteAuthor' => (string) $row['autor_cita'],
                'quoteYear' => (string) $row['anyo_cita'],
                'quoteContent' => (string) $row['contenido'],
                'quoteSectionOrder' => (string) $row['orden'],
                'quoteSectionTitle' => (string) $row['titulo']
            ],
            $rows
        );
    }
}