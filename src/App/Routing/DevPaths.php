<?php
declare(strict_types=1);

namespace App\App\Routing;


final class DevPaths
{
    private const BASE = 'dev/sesion-ejercicio';
    private const SIGUIENTE = 'dev/sesion-ejercicio/siguiente';
    private const RESET = 'dev/sesion-ejercicio/reset';

    public function base(): string
    {
        return self::BASE;
    }

    public function siguiente(): string
    {
        return self::SIGUIENTE;
    }

    public function reset(): string
    {
        return self::RESET;
    }
}