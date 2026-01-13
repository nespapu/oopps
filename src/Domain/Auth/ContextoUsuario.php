<?php

namespace App\Domain\Auth;

final class ContextoUsuario
{
    public function __construct(
        private readonly string $usuario,
        private readonly string $codigoOposicion
    ) {
        if ($this->usuario === '') {
            throw new \InvalidArgumentException('Usuario no puede estar vacío');
        }
        if ($this->codigoOposicion === '') {
            throw new \InvalidArgumentException('CodigoOposicion no puede estar vacío');
        }
    }

    public function usuario(): string
    {
        return $this->usuario;
    }

    public function codigoOposicion(): string
    {
        return $this->codigoOposicion;
    }
}
