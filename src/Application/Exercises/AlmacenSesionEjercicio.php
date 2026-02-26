<?php

declare(strict_types=1);

namespace App\Application\Exercises;

use App\Domain\Auth\ContextoUsuario;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;

interface AlmacenSesionEjercicio
{
    public function crear(
        ExerciseType $tipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): ExerciseSession;

    public function get(string $sesionId): ?ExerciseSession;

    public function getSesionActual(): ?ExerciseSession;

    public function guardar(ExerciseSession $sesion): void;

    public function borrar(string $sesionId): void;

    public function setSesionIdActual(string $sesionId): void;

    public function getSesionIdActual(): ?string;

    public function limpiarSesionIdActual(): void;

    public function purgarExpiradas(int $ttlSegundos): int;
}
