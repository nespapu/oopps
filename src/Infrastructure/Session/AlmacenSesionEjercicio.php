<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\SesionEjercicio;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;

final class AlmacenSesionEjercicio
{
    private const CLAVE_MAESTRA = 'ejercicio';
    private const CLAVE_SESIONES = 'sesiones';
    private const CLAVE_ID_ACTUAL = 'claveIdActual';

    public function __construct()
    {
        $this->instanciarAlmacenSesion();
    }

    public function crear(
        TipoEjercicio $tipoEjercicio,
        array $contextoUsuario,
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

        $bruto = $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES][$sesionId] ?? null;
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
        $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES][$sesion->sesionId()] = $this->deshidratar($sesion);
    }

    public function borrar(string $sesionId): void
    {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            return;
        }

        unset($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES][$sesionId]);

        if ($this->getSesionIdActual() === $sesionId) {
            $this->limpiarSesionIdActual();
        }
    }

    public function setSesionIdActual(string $sesionId): void
    {
        $sesionId = trim($sesionId);
        if ($sesionId === '') {
            throw new \InvalidArgumentException('claveIdActual no puede estar vacía.');
        }

        $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_ID_ACTUAL] = $sesionId;
    }

    public function getSesionIdActual(): ?string
    {
        $value = $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_ID_ACTUAL] ?? null;
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        return $value;
    }

    public function limpiarSesionIdActual(): void
    {
        unset($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_ID_ACTUAL]);
    }

    public function purgarExpiradas(int $ttlSegundos): int
    {
        if ($ttlSegundos <= 0) {
            return 0;
        }

        $ahora = new \DateTimeImmutable('now');
        $purgadas = 0;

        $sesiones = $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES] ?? [];
        if (!is_array($sesiones)) {
            return 0;
        }

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

            $tiempoTranscurridoSegundos = $ahora->getTimestamp() - $fechaActualizacion->getTimestamp();
            if ($tiempoTranscurridoSegundos > $ttlSegundos) {
                unset($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES][$sesionId]);
                $purgadas++;
            }
        }

        $sesionIdActual = $this->getSesionIdActual();
        if ($sesionIdActual !== null && !isset($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES][$sesionIdActual])) {
            $this->limpiarSesionIdActual();
        }

        return $purgadas;
    }

    private function instanciarAlmacenSesion(): void
    {
        if (!isset($_SESSION[self::CLAVE_MAESTRA]) || !is_array($_SESSION[self::CLAVE_MAESTRA])) {
            $_SESSION[self::CLAVE_MAESTRA] = [];
        }
        if (!isset($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES]) || !is_array($_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES])) {
            $_SESSION[self::CLAVE_MAESTRA][self::CLAVE_SESIONES] = [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function deshidratar(SesionEjercicio $sesion): array
    {
        $contextoUsuario = $sesion->contextoUsuario();

        return [
            'sesionId' => $sesion->sesionId(),
            'tipoEjercicio' => [
                'slug' => $sesion->tipoEjercicio()->slug(),
                'nombre' => $sesion->tipoEjercicio()->nombre(),
            ],
            'contextoUsuario' => [
                'usuario' => $contextoUsuario['usuario'],
                'oposicionId' => $contextoUsuario['oposicionId'],
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

        $usuarioBruto = $bruto['contextoUsuario'] ?? [];
        $contextoUsuario = [
            'usuario' => is_array($usuarioBruto) ? (string) ($usuarioBruto['usuario'] ?? '') : '',
            'oposicionId' => is_array($usuarioBruto) ? (string) ($usuarioBruto['oposicionId'] ?? '') : '',
        ];

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
