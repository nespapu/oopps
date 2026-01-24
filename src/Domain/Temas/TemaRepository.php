<?php

declare(strict_types=1);

namespace App\Domain\Temas;

interface TemaRepository
{
    public function buscarPorCodigoOposicion(string $codigoOposicion): array;

    public function buscarTituloPorCodigoOposicionYOrden(string $codigoOposicion, int $orden): ?string;

    public function buscarOrdenAleatorioPorCodigoOposicion(string $codigoOposicion): ?int;
}
