<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class ConfigEjercicio
{
    private int $tema;   // 0 = random
    private int $dificultad;   // 1..4
    private array $banderas;

    public function __construct(int $tema, int $dificultad, array $banderas = [])
    {
        if ($tema < 0) {
            throw new \InvalidArgumentException('Tema debe ser >= 0 (0 significa aleatorio).');
        }
        if ($dificultad < 1 || $dificultad > 4) {
            throw new \InvalidArgumentException('Dificultad debe estar entre 1 y 4.');
        }

        $banderasNormalizadas = [];
        foreach ($banderas as $clave => $valor) {
            $clave = trim((string) $clave);
            if ($clave === '') {
                continue;
            }
            $banderasNormalizadas[$clave] = (bool) $valor;
        }

        $this->tema = $tema;
        $this->dificultad = $dificultad;
        $this->banderas = $banderasNormalizadas;
    }

    public function tema(): int
    {
        return $this->tema;
    }

    public function esTemaAleatorio(): bool
    {
        return $this->tema === 0;
    }

    public function dificultad(): int
    {
        return $this->dificultad;
    }

    public function banderas(): array
    {
        return $this->banderas;
    }

    public function estaBanderaActivada(string $claveBandera, bool $defecto = false): bool
    {
        $claveBandera = trim($claveBandera);
        if ($claveBandera === '') {
            return $defecto;
        }

        return $this->flags[$claveBandera] ?? $defecto;
    }

    public function conBandera(string $claveBandera, bool $activada): self
    {
        $claveBandera = trim($claveBandera);
        if ($claveBandera === '') {
            return $this;
        }

        $banderas = $this->banderas;
        $banderas[$claveBandera] = $activada;

        return new self($this->tema, $this->dificultad, $banderas);
    }
}
