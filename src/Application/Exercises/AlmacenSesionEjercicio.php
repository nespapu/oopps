<?php

declare(strict_types=1);

namespace App\Application\Exercises;

use App\Domain\Auth\ContextoUsuario;
use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\SesionEjercicio;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;

interface AlmacenSesionEjercicio
{
    public function crear(
        TipoEjercicio $tipoEjercicio,
        ContextoUsuario $contextoUsuario,
        ConfigEjercicio $config,
        PasoEjercicio $primerPaso
    ): SesionEjercicio;

    public function get(string $sesionId): ?SesionEjercicio;

    public function getSesionActual(): ?SesionEjercicio;

    public function guardar(SesionEjercicio $sesion): void;

    public function borrar(string $sesionId): void;

    public function setSesionIdActual(string $sesionId): void;

    public function getSesionIdActual(): ?string;

    public function limpiarSesionIdActual(): void;

    public function purgarExpiradas(int $ttlSegundos): int;
}
