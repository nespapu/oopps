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
}
