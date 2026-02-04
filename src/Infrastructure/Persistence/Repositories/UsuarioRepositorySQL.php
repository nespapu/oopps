<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Auth\Usuario;
use App\Domain\Auth\UsuarioRepository;
use PDO;

final class UsuarioRepositorySQL implements UsuarioRepository 
{
    public function __construct(
        private readonly PDO $db
    ){}

    public function buscarPorNombre(string $nombre): ?Usuario
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

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute(['nombre' => $nombre]);

        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (!is_array($fila)) {
            return null;
        }

        return new Usuario(
            (string) $fila['nombre'], 
            (string) $fila['clave'], 
            (string) $fila['codigoOposicion']
        );
    }

}