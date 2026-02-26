<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

use App\Domain\Auth\ContextoUsuario;

final class SesionEjercicio
{
    private string $sesionId;
    private ExerciseType $TipoEjercicio;

    private ContextoUsuario $contextoUsuario;

    private ExerciseConfig $config;
    private ExerciseStep $currentStep;

    /** @var array<string, mixed> */
    private array $respuestasPorPaso;

    /** @var array<string, mixed> */
    private array $evaluacionPorPaso;

    private \DateTimeImmutable $fechaCreacion;
    private \DateTimeImmutable $fechaActualizacion;

    public function __construct(
        string $sesionId,
        ExerciseType $tipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ExerciseConfig $config,
        ExerciseStep $currentStep,
        \DateTimeImmutable $fechaCreacion,
        \DateTimeImmutable $fechaActualizacion,
        array $respuestasPorPaso = [],
        array $evaluacionPorPaso = []
    ) {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            throw new \InvalidArgumentException('sesionId no puede estar vacía.');
        }

        $this->sesionId = $sesionId;
        $this->TipoEjercicio = $tipoEjercicio;
        $this->contextoUsuario = $contextoUsuario;
        $this->config = $config;
        $this->currentStep = $currentStep;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaActualizacion = $fechaActualizacion;
        $this->respuestasPorPaso = $respuestasPorPaso;
        $this->evaluacionPorPaso = $evaluacionPorPaso;
    }

    public static function iniciar(
        ExerciseType $TipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): self {
        $now = new \DateTimeImmutable('now');

        return new self(
            self::generarSesionId(),
            $TipoEjercicio,
            $contextoUsuario,
            $config,
            $firstStep,
            $now,
            $now
        );
    }

    public function sesionId(): string
    {
        return $this->sesionId;
    }

    public function TipoEjercicio(): ExerciseType
    {
        return $this->TipoEjercicio;
    }

    public function contextoUsuario(): ContextoUsuario
    {
        return $this->contextoUsuario;
    }

    public function config(): ExerciseConfig
    {
        return $this->config;
    }

    public function pasoActual(): ExerciseStep
    {
        return $this->currentStep;
    }

    public function fechaCreacion(): \DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function fechaActualizacion(): \DateTimeImmutable
    {
        return $this->fechaActualizacion;
    }

    public function moverAlPaso(ExerciseStep $step): void
    {
        $this->currentStep = $step;
        $this->actualizar();
    }

    /**
     * @param mixed $respuestaDto
     */
    public function setRespuestaPaso(ExerciseStep $step, mixed $respuestaDto): void
    {
        $this->respuestasPorPaso[$step->value] = $respuestaDto;
        $this->actualizar();
    }

    /**
     * @param mixed $evaluacionDto
     */
    public function setEvaluacionPaso(ExerciseStep $step, mixed $evaluacionDto): void
    {
        $this->evaluacionPorPaso[$step->value] = $evaluacionDto;
        $this->actualizar();
    }

    public function getRespuestaPaso(ExerciseStep $step): mixed
    {
        return $this->respuestasPorPaso[$step->value] ?? null;
    }

    public function getEvaluacionPaso(ExerciseStep $paso): mixed
    {
        return $this->evaluacionPorPaso[$paso->value] ?? null;
    }

    private function actualizar(): void
    {
        $this->fechaActualizacion = new \DateTimeImmutable('now');
    }

    private static function generarSesionId(): string
    {
        // 32 hex chars
        return bin2hex(random_bytes(16));
    }
}