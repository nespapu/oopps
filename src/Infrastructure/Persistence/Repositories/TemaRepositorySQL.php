<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Temas\TemaRepository;
use App\Infrastructure\Persistence\ConexionBD;
use PDO;

final class TemaRepositorySQL implements TemaRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? ConexionBD::obtener();
    }

    public function buscarPorCodigoOposicion(string $codigoOposicion): array
    {
        $sql = "
            SELECT
                numeracion AS `numeracion`,
                titulo AS `titulo` 
            FROM tema 
            WHERE codigo_oposicion = :codigoOposicion 
            ORDER BY numeracion ASC
        ";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute(['codigoOposicion' => $codigoOposicion]);

        $filas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $fila): array => [
                'numeracion' => (int) $fila['numeracion'],
                'titulo' => (string) $fila['titulo'],
            ],
            $filas
        );
    }

    public function buscarTituloPorCodigoOposicionYOrden(string $codigoOposicion, int $orden): ?string
    {
        $sql = "
            SELECT titulo AS `titulo`
            FROM tema
            WHERE codigo_oposicion = :codigoOposicion
              AND orden = :orden
            LIMIT 1
        ";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([
            'codigoOposicion' => $codigoOposicion,
            'orden'   => $orden,
        ]);

        $titulo = $sentencia->fetchColumn();

        if ($titulo === false) {
            return null;
        }

        return (string) $titulo;
    }


    public function buscarOrdenAleatorioPorCodigoOposicion(string $codigoOposicion): ?int
    {
        $sql = "
            SELECT orden AS `orden`
            FROM tema
            WHERE codigo_oposicion = :codigoOposicion
            ORDER BY RAND()
            LIMIT 1
        ";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute(['codigoOposicion' => $codigoOposicion]);

        $orden = $sentencia->fetchColumn();

        if ($orden === false) {
            return null;
        }

        return (int) $orden;
    }
}
