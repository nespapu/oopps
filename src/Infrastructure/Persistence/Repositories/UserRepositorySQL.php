<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use PDO;

final class UserRepositorySQL implements UserRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    public function findByName(string $name): ?User
    {
        $sql = "
            SELECT 
                nombre as `nombre`,
                clave as `clave`,
                codigo_oposicion as `codigoOposicion`
            FROM usuario
            WHERE nombre = :nombre
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['nombre' => $name]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }

        return new User(
            (string) $row['nombre'],
            (string) $row['clave'],
            (string) $row['codigoOposicion']
        );
    }
}