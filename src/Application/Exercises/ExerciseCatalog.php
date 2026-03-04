<?php

declare(strict_types=1);

namespace App\Application\Exercises;

use App\Domain\Exercise\ExerciseType;

final class ExerciseCatalog
{
    /**
     * @return array<string, array{type: ExerciseType, path: string, is_active: bool}>
     */
    public static function all(): array
    {
        $howMuch = ExerciseType::howMuchDoYouKnowTopic();
        $examSim = ExerciseType::theoreticalExamSimulation();

        return [
            $examSim->slug() => [
                'type' => $examSim,
                'path' => 'ejercicio/simulacro-examen-teorico',
                'is_active' => true,
            ],
            $howMuch->slug() => [
                'type' => $howMuch,
                'path' => 'ejercicios/cuanto-sabes-tema/config',
                'is_active' => true,
            ],
        ];
    }
}
