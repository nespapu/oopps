<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class SesionEjercicio
{
    private string $sesionId;
    private TipoEjercicio $TipoEjercicio;

    /** @var array{nombre: string, oposicionId: string} */
    private array $contextoUsuario;

    private ConfigEjercicio $config;
    private PasoEjercicio $pasoActual;

    /** @var array<string, mixed> */
    private array $respuestasPorPaso;

    /** @var array<string, mixed> */
    private array $evaluacionPorPaso;

    private \DateTimeImmutable $fechaCreacion;
    private \DateTimeImmutable $fechaActualizacion;

    public function __construct(
        string $sesionId,
        TipoEjercicio $TipoEjercicio,
        array $contextoUsuario,
        ConfigEjercicio $config,
        PasoEjercicio $pasoActual,
        \DateTimeImmutable $fechaCreacion,
        \DateTimeImmutable $fechaActualizacion,
        array $respuestasPorPaso = [],
        array $evaluacionPorPaso = []
    ) {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            throw new \InvalidArgumentException('sesionId no puede estar vacía.');
        }

        $usuario = trim($contextoUsuario['usuario'] ?? '');
        $oposicionId = trim($contextoUsuario['oposicionId'] ?? '');

        if ($usuario === '') {
            throw new \InvalidArgumentException('contextoUsuario.usuario no puede ser vacío.');
        }
        if ($oposicionId === '') {
            throw new \InvalidArgumentException('contextoUsuario.oposicionId no puede ser vacío.');
        }

        $this->sesionId = $sesionId;
        $this->TipoEjercicio = $TipoEjercicio;
        $this->contextoUsuario = [
            'usuario' => $usuario,
            'oposicionId' => $oposicionId,
        ];
        $this->config = $config;
        $this->pasoActual = $pasoActual;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaActualizacion = $fechaActualizacion;
        $this->respuestasPorPaso = $respuestasPorPaso;
        $this->evaluacionPorPaso = $evaluacionPorPaso;
    }

    public static function iniciar(
        TipoEjercicio $TipoEjercicio,
        array $contextoUsuario,
        ConfigEjercicio $config,
        PasoEjercicio $primerPaso
    ): self {
        $now = new \DateTimeImmutable('now');

        return new self(
            self::generarSesionId(),
            $TipoEjercicio,
            $contextoUsuario,
            $config,
            $primerPaso,
            $now,
            $now
        );
    }

    public function sesionId(): string
    {
        return $this->sesionId;
    }

    public function TipoEjercicio(): TipoEjercicio
    {
        return $this->TipoEjercicio;
    }

    /**
     * @return array{usuario: string, oposicionId: string}
     */
    public function contextoUsuario(): array
    {
        return $this->contextoUsuario;
    }

    public function config(): ConfigEjercicio
    {
        return $this->config;
    }

    public function pasoActual(): PasoEjercicio
    {
        return $this->pasoActual;
    }

    public function fechaCreacion(): \DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function fechaActualizacion(): \DateTimeImmutable
    {
        return $this->fechaActualizacion;
    }

    public function moverAlPaso(PasoEjercicio $paso): void
    {
        $this->pasoActual = $paso;
        $this->actualizar();
    }

    /**
     * @param mixed $respuestaDto
     */
    public function setRespuestaPaso(PasoEjercicio $paso, mixed $respuestaDto): void
    {
        $this->respuestasPorPaso[$paso->value] = $respuestaDto;
        $this->actualizar();
    }

    /**
     * @param mixed $evaluacionDto
     */
    public function setEvaluacionPaso(PasoEjercicio $paso, mixed $evaluacionDto): void
    {
        $this->evaluacionPorPaso[$paso->value] = $evaluacionDto;
        $this->actualizar();
    }

    public function getRespuestaPaso(PasoEjercicio $paso): mixed
    {
        return $this->respuestasPorPaso[$paso->value] ?? null;
    }

    public function getEvaluacionPaso(PasoEjercicio $paso): mixed
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