<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\BookRepository;
use PDO;

final class SqlBookRepository implements BookRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    /**
     * @return array<int, array{
     *      bookTitle: string, 
     *      bookAuthor: string,
     *      bookPublisher: string,
     *      bookPublicationYear: string
     * }>
     */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $sql = "
            SELECT 
                l.autor,
                l.anyo,
                l.titulo,
                l.editorial
            FROM tema_referenciar_libro AS trl
            INNER JOIN libro AS l
                ON l.autor = trl.autor_libro
                AND l.titulo = trl.titulo_libro
            WHERE trl.codigo_oposicion = :codigoOposicion
                AND trl.orden_tema = :ordenTema
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): array => [
                'bookAuthor' => (string) $row['autor'],
                'bookPublicationYear' => (string) $row['anyo'],
                'bookTitle' => (string) $row['titulo'],
                'bookPublisher' => (string) $row['editorial']
            ],
            $rows
        );
    }
}