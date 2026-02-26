<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

enum ExerciseStep: string
{
    case TITLE = 'titulo';
    case INDEX = 'indice';
    case JUSTIFICATION = 'justificacion';
    case QUOTES = 'citas';
    case TOOLS = 'herramientas';
    case SCHOOL_CONTEXT = 'contexto-escolar';
    case WORK_CONTEXT = 'contexto-laboral';
    case BIBLIOGRAPHY = 'bibliografia';
    case WEBGRAPHY = 'webgrafia';

    public function label(): string
    {
        return match ($this) {
            self::TITLE => 'Título',
            self::INDEX => 'Índice',
            self::JUSTIFICATION => 'Justificación',
            self::QUOTES => 'Citas',
            self::TOOLS => 'Herramientas',
            self::SCHOOL_CONTEXT => 'Contexto escolar',
            self::WORK_CONTEXT => 'Contexto laboral',
            self::BIBLIOGRAPHY => 'Bibliografía',
            self::WEBGRAPHY => 'Webgrafía',
        };
    }

    /**
     * Defines the wizard step order (starting at 0).
     */
    public function order(): int
    {
        return array_search($this, self::sequence(), true);
    }

    /**
     * @return array<self>
     */
    public static function sequence(): array
    {
        return [
            self::TITLE,
            self::INDEX,
            self::JUSTIFICATION,
            self::QUOTES,
            self::TOOLS,
            self::SCHOOL_CONTEXT,
            self::WORK_CONTEXT,
            self::BIBLIOGRAPHY,
            self::WEBGRAPHY,
        ];
    }

    public function isFirst(): bool
    {
        return $this === self::sequence()[0];
    }

    public function isLast(): bool
    {
        $sequence = self::sequence();
        return $this === $sequence[count($sequence) - 1];
    }

    public function next(): ?self
    {
        $sequence = self::sequence();
        $index = array_search($this, $sequence, true);

        if ($index === false) {
            return null;
        }

        $nextIndex = $index + 1;

        return $sequence[$nextIndex] ?? null;
    }

    public function previous(): ?self
    {
        $sequence = self::sequence();
        $index = array_search($this, $sequence, true);

        if ($index === false) {
            return null;
        }

        $previousIndex = $index - 1;

        return $sequence[$previousIndex] ?? null;
    }

    public static function first(): self
    {
        return self::sequence()[0];
    }
}