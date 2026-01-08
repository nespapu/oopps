<?php

namespace App\Core\Routes\Dev;

final class RutasDevSesionEjercicio
{
    public const BASE = 'dev/sesion-ejercicio';
    public const SIGUIENTE = 'dev/sesion-ejercicio/siguiente';
    public const RESET = 'dev/sesion-ejercicio/reset';

    /**
     * @return array<int, string>
     */
    public static function patrones(): array
    {
        return [
            self::BASE,
            self::SIGUIENTE,
            self::RESET,
        ];
    }
}
?>