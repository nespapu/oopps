<?php

namespace App\Core;

final class CanonizadorRuta
{
    /**
     * @param string $ruta Ruta actual, e.g. "ejercicios/.../sessions/abc/paso/titulo"
     * @param array<int, string> $patrones Lista de patrones con {params}
     * @return array{0:string,1:array<string,string>} [rutaCanonica, params]
     */
    public static function canonizar(string $ruta, array $patrones): array
    {
        foreach ($patrones as $patron) {
            $params = self::match($patron, $ruta);
            if ($params !== null) {
                return [$patron, $params];
            }
        }

        return [$ruta, []];
    }

    /**
     * @return array<string,string>|null
     */
    private static function match(string $patron, string $ruta): ?array
    {
        $paramNombres = [];

        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function ($m) use (&$paramNombres) {
                $paramNombres[] = $m[1];
                return '([^/]+)';
            },
            $patron
        );

        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $ruta, $matches)) {
            return null;
        }

        array_shift($matches);

        $params = [];
        foreach ($paramNombres as $i => $name) {
            $params[$name] = $matches[$i] ?? '';
        }

        return $params;
    }
}
