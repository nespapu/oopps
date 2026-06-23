<?php


declare(strict_types=1);


namespace App\Infrastructure\Persistence\Repositories;


use App\Domain\Temas\JustificationRepository;
use PDO;


final class SqlJustificationRepository implements JustificationRepository
{
    public function __construct(
        private readonly PDO $db
    ) {}

    /**
    * @return array<int, array{cycleName: string, laws: array<int, string>, modules: array<int, string>}>
    */
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        $justification = [];

        $cyclesSql = "
            SELECT 
                nombre_ciclo 
            FROM 
                tema_enmarcar_ciclo 
            WHERE 
                codigo_oposicion = :codigoOposicion AND 
                orden_tema = :ordenTema
        ";

        $cyclesStatement = $this->db->prepare($cyclesSql);

        $cyclesStatement->execute([
            'codigoOposicion' => $oppositionCode,
            'ordenTema' => $topicOrder,
        ]);

        $lawSql = "
            SELECT
                nombre_ley
            FROM
                ley_definir_ciclo
            WHERE
                nombre_ciclo = :nombreCiclo
        ";

        $lawStatement = $this->db->prepare($lawSql);

        $moduleSql = "
            SELECT
                mdt.nombre_modulo as modulo
            FROM
                ciclo_impartir_modulo cim
            JOIN
                modulo_desarrollar_tema mdt ON mdt.nombre_modulo = cim.nombre_modulo
            WHERE
                cim.nombre_ciclo = :nombreCiclo AND
                mdt.orden_tema = :ordenTema
        ";

        $moduleStatement = $this->db->prepare($moduleSql);

        foreach ($cyclesStatement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cycle = [
                'cycleName' => $row['nombre_ciclo'],
                'laws' => [],
                'modules' => [],
            ];

            $lawStatement->execute([
                'nombreCiclo' => $row['nombre_ciclo'],
            ]);

            foreach ($lawStatement->fetchAll(PDO::FETCH_ASSOC) as $lawRow) {
                $cycle['laws'][] = $lawRow['nombre_ley'];
            }

            $moduleStatement->execute([
                'nombreCiclo' => $row['nombre_ciclo'],
                'ordenTema' => $topicOrder,
            ]);

            foreach ($moduleStatement->fetchAll(PDO::FETCH_ASSOC) as $moduleRow) {
                $cycle['modules'][] = $moduleRow['modulo'];
            }

            $justification[] = $cycle;
        }
        return $justification;
    }
}