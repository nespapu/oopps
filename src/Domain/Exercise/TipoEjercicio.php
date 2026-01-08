<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class TipoEjercicio
{
    private string $slug;
    private string $nombre;

    private function __construct(string $slug, string $nombre)
    {
        $slug = trim($slug);
        $nombre = trim($nombre);

        if ($slug === '') {
            throw new \InvalidArgumentException('TipoEjercicio slug no puede estar vacío.');
        }
        if ($nombre === '') {
            throw new \InvalidArgumentException('TipoEjercicio nombre no puede estar vacío.');
        }

        $this->slug = $slug;
        $this->nombre = $nombre;
    }

    public static function cuantoSabesTema(): self
    {
        return new self('cuanto-sabes-tema', 'Cuánto sabes del tema');
    }

    public static function simulacroExamenTeorico(): self
    {
        return new self('simulacro-examen-teorico', 'Simulacro examen teórico');
    }

    public static function todos(): array
    {
        $tipos = [
            self::cuantoSabesTema(),
            self::simulacroExamenTeorico(),
        ];

        $porSlug = [];
        foreach ($tipos as $tipo) {
            $porSlug[$tipo->slug()] = $tipo;
        }

        return $porSlug;
    }

    public static function desdeSlug(string $slug): self
    {
        $slug = trim($slug);
        $todos = self::todos();

        if (!isset($todos[$slug])) {
            throw new \InvalidArgumentException("TipoEjercicio slug desconocido: {$slug}");
        }

        return $todos[$slug];
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function igual(self $otro): bool
    {
        return $this->slug === $otro->slug;
    }
}
