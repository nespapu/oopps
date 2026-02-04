<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use App\Application\Exercises\AlmacenSesionEjercicio;
use App\Application\Session\SessionStore;
use App\Domain\Auth\ContextoUsuario;
use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\SesionEjercicio;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;

final class PhpAlmacenSesionEjercicio implements AlmacenSesionEjercicio
{
    private const CLAVE_ALMACEN = 'almacen_ejercicio_v1';

    public function __construct(
        private SessionStore $sessionStore
    ) {}

    public function crear(
        TipoEjercicio $tipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ConfigEjercicio $config,
        PasoEjercicio $primerPaso
    ): SesionEjercicio {
        $sesion = SesionEjercicio::iniciar($tipoEjercicio, $contextoUsuario, $config, $primerPaso);

        $this->guardar($sesion);
        $this->setSesionIdActual($sesion->sesionId());

        return $sesion;
    }

    public function get(string $sesionId): ?SesionEjercicio
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

    public function getSesionActual(): ?SesionEjercicio
    {
        $sesionIdActual = $this->getSesionIdActual();
        if ($sesionIdActual === null) {
            return null;
        }

        return $this->get($sesionIdActual);
    }

    public function guardar(SesionEjercicio $sesion): void
    {
        $data = $this->leerAlmacen();
        $data['sesiones'][$sesion->sesionId()] = $this->deshidratar($sesion);

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
    private function deshidratar(SesionEjercicio $sesion): array
    {
        return [
            'sesionId' => $sesion->sesionId(),
            'tipoEjercicio' => [
                'slug' => $sesion->tipoEjercicio()->slug(),
                'nombre' => $sesion->tipoEjercicio()->nombre(),
            ],
            'contextoUsuario' => [
                'usuario' => $sesion->contextoUsuario()->usuario(),
                'codigoOposicion' => $sesion->contextoUsuario()->codigoOposicion(),
            ],
            'config' => [
                'tema' => $sesion->config()->tema(),
                'dificultad' => $sesion->config()->dificultad(),
                'banderas' => $sesion->config()->banderas(),
            ],
            'pasoActual' => $sesion->pasoActual()->value,
            // these are intentionally raw; keep DTO-ish, not domain objects
            'respuestasPorPaso' => $this->safeArray($this->readPrivateProperty($sesion, 'respuestasPorPaso')),
            'evaluacionPorPaso' => $this->safeArray($this->readPrivateProperty($sesion, 'evaluacionPorPaso')),
            'fechaCreacion' => $sesion->fechaCreacion()->format(\DateTimeInterface::ATOM),
            'fechaActualizacion' => $sesion->fechaActualizacion()->format(\DateTimeInterface::ATOM),
        ];
    }

    private function hidratar(array $bruto): SesionEjercicio
    {
        $sesionId = (string) ($bruto['sesionId'] ?? '');

        $tipoBruto = $bruto['tipoEjercicio'] ?? [];
        $tipoSlug = is_array($tipoBruto) ? (string) ($tipoBruto['slug'] ?? '') : '';
        $tipoEjercicio = TipoEjercicio::desdeSlug($tipoSlug);

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

        $config = new ConfigEjercicio($tema, $dificultad, $banderas);

        $pasoBruto = (string) ($bruto['pasoActual'] ?? PasoEjercicio::primero()->value);
        $pasoActual = PasoEjercicio::from($pasoBruto);

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

        return new SesionEjercicio(
            $sesionId,
            $tipoEjercicio,
            $contextoUsuario,
            $config,
            $pasoActual,
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
