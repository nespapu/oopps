<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\WebsiteRepository;
use PDO;

final class SqlWebsiteRepository implements WebsiteRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    /**
     * @return array<int, array{
     *      websiteName: string, 
     *      websiteURL: string,
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT 
                w.nombre,
                w.url
            FROM tema_referenciar_web AS trw
            INNER JOIN web AS w
                ON w.url = trw.url_web
            WHERE trw.codigo_oposicion = :codigoOposicion
                AND trw.orden_tema = :ordenTema
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'websiteName' => (string) $row['nombre'],
                'websiteURL' => (string) $row['url'],
            ],
            $rows
        );
    }
}