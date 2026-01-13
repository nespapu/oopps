<?php

namespace App\Core\Routes;

final class RutasCuantoSabesTema {
    public const CONFIG = 'ejercicios/cuanto-sabes-tema/config';
    public const INICIO  = 'ejercicios/cuanto-sabes-tema/inicio';

    public const PASO_TITULO = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo';
    public const EVAL_TITULO = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo/evaluar';

    /**
     * @return array<int, string>
     */
    public static function patrones(): array {
        return [
            self::CONFIG,
            self::INICIO,
            self::PASO_TITULO,
            self::EVAL_TITULO,
        ];
    }

    public static function pasoTitulo(string $sesionId): string
    {
        return self::rellenar(self::PASO_TITULO, [
            'sesionId' => $sesionId,
        ]);
    }

    public static function evaluarTitulo(string $sesionId): string
    {
        return self::rellenar(self::EVAL_TITULO, [
            'sesionId' => $sesionId,
        ]);
    }

    /**
     * @param array<string, string> $parametros
     */
    private static function rellenar(string $ruta, array $parametros): string
    {
        foreach ($parametros as $clave => $valor) {
            $ruta = str_replace(
                '{' . $clave . '}',
                rawurlencode($valor),
                $ruta
            );
        }

        return $ruta;
    }
}
