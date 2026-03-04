<?php

declare(strict_types=1);

namespace App\Application\Exercises;

final class ExerciseCatalog
{
    public static function all(): array
    {
        return [
            [
                'name' => 'Simulacro examen teórico',
                'path' => 'ejercicio/simulacro-examen-teorico',
                'is_active' => true,
            ],
            [
                'name' => 'Cuánto sabes del tema',
                'path' => 'ejercicios/cuanto-sabes-tema/config',
                'is_active' => true,
            ],
        ];
    }
}