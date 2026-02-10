<?php
declare(strict_types=1);

namespace App\App\Routing;


final class AuthPaths
{
    private const LOGIN = 'login';
    private const LOGOUT = 'login/salir';

    public function login(): string
    {
        return self::LOGIN;
    }

    public function logout(): string
    {
        return self::LOGOUT;
    }
}