<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use App\Application\Exercises\AlmacenSesionEjercicio;
use App\Application\Session\SessionStore;
use App\Domain\Auth\ContextoUsuario;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;

final class PhpAlmacenSesionEjercicio implements AlmacenSesionEjercicio
{
    private const CLAVE_ALMACEN = 'almacen_ejercicio_v1';

    public function __construct(
        private SessionStore $sessionStore
    ) {}

    public function crear(
        ExerciseType $tipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): ExerciseSession {
        $sesion = ExerciseSession::start($tipoEjercicio, $contextoUsuario, $config, $firstStep);

        $this->guardar($sesion);
        $this->setSesionIdActual($sesion->sessionId());

        return $sesion;
    }

    public function get(string $sesionId): ?ExerciseSession
    {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            return null;
        }

        $data = $this->leerAlmacen();
        $bruto = $data['sesiones'][$sesionId] ?? null;

        if (!is_array($bruto)) {
            return null;
        }

        try {
            return $this->hidratar($bruto);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getSesionActual(): ?ExerciseSession
    {
        $sesionIdActual = $this->getSesionIdActual();
        if ($sesionIdActual === null) {
            return null;
        }

        return $this->get($sesionIdActual);
    }

    public function guardar(ExerciseSession $sesion): void
    {
        $data = $this->leerAlmacen();
        $data['sesiones'][$sesion->sessionId()] = $this->deshidratar($sesion);

        $this->escribirAlmacen($data);
    }

    public function borrar(string $sesionId): void
    {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            return;
        }

        $data = $this->leerAlmacen();
        unset($data['sesiones'][$sesionId]);

        if (($data['sesionIdActual'] ?? null) === $sesionId) {
            $data['sesionIdActual'] = null;
        }

        $this->escribirAlmacen($data);
    }

    public function setSesionIdActual(string $sesionId): void
    {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            throw new \InvalidArgumentException('sesionId no puede estar vacía.');
        }

        $data = $this->leerAlmacen();
        $data['sesionIdActual'] = $sesionId;

        $this->escribirAlmacen($data);
    }

    public function getSesionIdActual(): ?string
    {
        $data = $this->leerAlmacen();
        $value = $data['sesionIdActual'] ?? null;

        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        return $value;
    }

    public function limpiarSesionIdActual(): void
    {
        $data = $this->leerAlmacen();
        $data['sesionIdActual'] = null;

        $this->escribirAlmacen($data);
    }

    public function purgarExpiradas(int $ttlSegundos): int
    {
        if ($ttlSegundos <= 0) {
            return 0;
        }

        $data = $this->leerAlmacen();
        $sesiones = $data['sesiones'] ?? [];
        if (!is_array($sesiones)) {
            return 0;
        }

        $ahora = new \DateTimeImmutable('now');
        $purgadas = 0;

        foreach ($sesiones as $sesionId => $bruto) {
            if (!is_array($bruto)) {
                continue;
            }

            $fechaActualizacionBruto = $bruto['fechaActualizacion'] ?? null;
            if (!is_string($fechaActualizacionBruto) || $fechaActualizacionBruto === '') {
                continue;
            }

            try {
                $fechaActualizacion = new \DateTimeImmutable($fechaActualizacionBruto);
            } catch (\Throwable) {
                continue;
            }

            $elapsed = $ahora->getTimestamp() - $fechaActualizacion->getTimestamp();
            if ($elapsed > $ttlSegundos) {
                unset($sesiones[$sesionId]);
                $purgadas++;
            }
        }

        $data['sesiones'] = $sesiones;

        $sesionIdActual = $data['sesionIdActual'] ?? null;
        if (is_string($sesionIdActual) && $sesionIdActual !== '' && !isset($sesiones[$sesionIdActual])) {
            $data['sesionIdActual'] = null;
        }

        $this->escribirAlmacen($data);

        return $purgadas;
    }

    /**
     * @return array{sesiones: array<string, array<string, mixed>>, sesionIdActual: string|null}
     */
    private function leerAlmacen(): array
    {
        $raw = $this->sessionStore->getString(self::CLAVE_ALMACEN);
        if ($raw === null || trim($raw) === '') {
            return ['sesiones' => [], 'sesionIdActual' => null];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return ['sesiones' => [], 'sesionIdActual' => null];
        }

        if (!is_array($decoded)) {
            return ['sesiones' => [], 'sesionIdActual' => null];
        }

        $sesiones = $decoded['sesiones'] ?? [];
        $sesionIdActual = $decoded['sesionIdActual'] ?? null;

        return [
            'sesiones' => is_array($sesiones) ? $sesiones : [],
            'sesionIdActual' => is_string($sesionIdActual) && trim($sesionIdActual) !== '' ? $sesionIdActual : null,
        ];
    }

    /**
     * @param array{sesiones: array<string, array<string, mixed>>, sesionIdActual: string|null} $data
     */
    private function escribirAlmacen(array $data): void
    {
        $payload = json_encode($data, JSON_THROW_ON_ERROR);
        $this->sessionStore->setString(self::CLAVE_ALMACEN, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function deshidratar(ExerciseSession $sesion): array
    {
        return [
            'sesionId' => $sesion->sessionId(),
            'tipoEjercicio' => [
                'slug' => $sesion->exerciseType()->slug(),
                'nombre' => $sesion->exerciseType()->name(),
            ],
            'contextoUsuario' => [
                'usuario' => $sesion->userContext()->usuario(),
                'codigoOposicion' => $sesion->userContext()->codigoOposicion(),
            ],
            'config' => [
                'tema' => $sesion->config()->topicId(),
                'dificultad' => $sesion->config()->difficulty(),
                'banderas' => $sesion->config()->flags(),
            ],
            'pasoActual' => $sesion->currentStep()->value,
            // these are intentionally raw; keep DTO-ish, not domain objects
            'respuestasPorPaso' => $this->safeArray($this->readPrivateProperty($sesion, 'respuestasPorPaso')),
            'evaluacionPorPaso' => $this->safeArray($this->readPrivateProperty($sesion, 'evaluacionPorPaso')),
            'fechaCreacion' => $sesion->createdAt()->format(\DateTimeInterface::ATOM),
            'fechaActualizacion' => $sesion->updatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    private function hidratar(array $bruto): ExerciseSession
    {
        $sesionId = (string) ($bruto['sesionId'] ?? '');

        $tipoBruto = $bruto['tipoEjercicio'] ?? [];
        $tipoSlug = is_array($tipoBruto) ? (string) ($tipoBruto['slug'] ?? '') : '';
        $tipoEjercicio = ExerciseType::fromSlug($tipoSlug);

        $contextoUsuarioBruto = $bruto['contextoUsuario'] ?? [];
        $contextoUsuarioBruto = [
            'usuario' => is_array($contextoUsuarioBruto) ? (string) ($contextoUsuarioBruto['usuario'] ?? '') : '',
            'codigoOposicion' => is_array($contextoUsuarioBruto) ? (string) ($contextoUsuarioBruto['codigoOposicion'] ?? '') : '',
        ];
        $contextoUsuario = new ContextoUsuario($contextoUsuarioBruto['usuario'], $contextoUsuarioBruto['codigoOposicion']);

        $configBruto = $bruto['config'] ?? [];
        $tema = is_array($configBruto) ? (int) ($configBruto['tema'] ?? 0) : 0;
        $dificultad = is_array($configBruto) ? (int) ($configBruto['dificultad'] ?? 1) : 1;
        $banderas = is_array($configBruto) ? ($configBruto['banderas'] ?? []) : [];
        if (!is_array($banderas)) {
            $banderas = [];
        }

        $config = new ExerciseConfig($tema, $dificultad, $banderas);

        $rawStep = (string) ($bruto['pasoActual'] ?? ExerciseStep::first()->value);
        $currentStep = ExerciseStep::from($rawStep);

        $fechaCreacion = new \DateTimeImmutable((string) ($bruto['fechaCreacion'] ?? 'now'));
        $fechaActualizacion = new \DateTimeImmutable((string) ($bruto['fechaActualizacion'] ?? 'now'));

        $respuestasPorPaso = $bruto['respuestasPorPaso'] ?? [];
        $evaluacionPorPaso = $bruto['evaluacionPorPaso'] ?? [];

        if (!is_array($respuestasPorPaso)) {
            $respuestasPorPaso = [];
        }
        if (!is_array($evaluacionPorPaso)) {
            $evaluacionPorPaso = [];
        }

        return new ExerciseSession(
            $sesionId,
            $tipoEjercicio,
            $contextoUsuario,
            $config,
            $currentStep,
            $fechaCreacion,
            $fechaActualizacion,
            $respuestasPorPaso,
            $evaluacionPorPaso
        );
    }

    /**
     * @param mixed $value
     * @return array<string, mixed>
     */
    private function safeArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * Escape hatch: since ExerciseSession keeps these arrays private,
     * we read them via reflection to avoid changing your domain model right now.
     * If you prefer, we can instead add public getters in ExerciseSession and delete this.
     */
    private function readPrivateProperty(object $object, string $property): mixed
    {
        $ref = new \ReflectionClass($object);
        if (!$ref->hasProperty($property)) {
            return null;
        }

        $prop = $ref->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($object);
    }
}
