<?php

declare(strict_types=1);

namespace App\Domain\Auth;

interface UsuarioRepository
{
    public function buscarPorNombre(string $nombre): ?Usuario;
}
