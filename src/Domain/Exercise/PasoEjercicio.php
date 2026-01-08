<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

enum PasoEjercicio: string
{
    case TITULO = 'titulo';
    case INDICE = 'indice';
    case JUSTIFICACION = 'justificacion';
    case CITAS = 'citas';
    case HERRAMIENTAS = 'herramientas';
    case CONTEXTO_ESCOLAR = 'contexto-escolar';
    case CONTEXTO_LABORAL = 'contexto-laboral';
    case BIBLIOGRAFIA = 'bibliografia';
    case WEBGRAFIA = 'webgrafia';

    public function etiqueta(): string
    {
        return match ($this) {
            self::TITULO => 'Título',
            self::INDICE => 'Índice',
            self::JUSTIFICACION => 'Justificación',
            self::CITAS => 'Citas',
            self::HERRAMIENTAS => 'Herramientas',
            self::CONTEXTO_ESCOLAR => 'Contexto escolar',
            self::CONTEXTO_LABORAL => 'Contexto laboral',
            self::BIBLIOGRAFIA => 'Bibliografía',
            self::WEBGRAFIA => 'Webgrafía',
        };
    }

    /**
     * Define el orden del paso del wizard (empezando en 0).
     */
    public function orden(): int
    {
        return array_search($this, self::secuencia(), true);
    }

    public static function secuencia(): array
    {
        return [
            self::TITULO,
            self::INDICE,
            self::JUSTIFICACION,
            self::CITAS,
            self::HERRAMIENTAS,
            self::CONTEXTO_ESCOLAR,
            self::CONTEXTO_LABORAL,
            self::BIBLIOGRAFIA,
            self::WEBGRAFIA,
        ];
    }

    public function esPrimero(): bool
    {
        return $this === self::secuencia()[0];
    }

    public function esUltimo(): bool
    {
        $seq = self::secuencia();
        return $this === $seq[count($seq) - 1];
    }

    public function siguiente(): ?self
    {
        $seq = self::secuencia();
        $i = array_search($this, $seq, true);

        if ($i === false) {
            return null;
        }

        $siguiente = $i + 1;

        return $seq[$siguiente] ?? null;
    }

    public function previo(): ?self
    {
        $seq = self::secuencia();
        $i = array_search($this, $seq, true);

        if ($i === false) {
            return null;
        }

        $previo = $i - 1;

        return $seq[$previo] ?? null;
    }

    public static function primero(): self
    {
        return self::secuencia()[0];
    }
}
