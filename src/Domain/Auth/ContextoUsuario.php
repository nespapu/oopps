<?php

namespace App\Domain\Auth;

final class ContextoUsuario
{
    private readonly string $usuario;
    private readonly string $codigoOposicion;

    public function __construct($usuario, $codigoOposicion) {
        $this->usuario = trim($usuario);
        $this->codigoOposicion = trim($codigoOposicion);

        if ($this->usuario === '') {
            throw new \InvalidArgumentException('usuario no puede estar vacío');
        }
        if ($this->codigoOposicion === '') {
            throw new \InvalidArgumentException('codigoOposicion no puede estar vacío');
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
