<?php

namespace App\Application\Exercises\Evaluation;

final class CuantoSabesTemaTituloEvaluationService
{
    public function evaluar(string $respuesta, string $solucion): bool
    {
        $respuesta = trim($respuesta);
        $solucion = trim($solucion);

        return $respuesta === $solucion;
    }
}
?>