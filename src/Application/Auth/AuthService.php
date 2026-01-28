<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Auth\ContextoUsuario;

interface AuthService
{
    public function requireLogin(): void;

    public function requireOppositionContext(): void;

    public function isLoggedIn(): bool;

    public function username(): ?string;

    public function oppositionCode(): ?string;

    public function userContext(): ContextoUsuario;

    public function logout(): void;
}
