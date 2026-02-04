<?php

declare(strict_types=1);

namespace App\Domain\Auth;

final class Usuario
{
    public function __construct(
        private string $nombre,
        private string $clave,
        private string $codigoOposicion
    ) {}

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function clave(): string
    {
        return $this->clave;
    }

    public function codigoOposicion(): string
    {
        return $this->codigoOposicion;
    }
}
