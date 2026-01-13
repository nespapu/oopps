<?php
namespace App\Domain\Exercise;

enum Dificultad: int
{
    case MUY_FACIL = 1;
    case FACIL     = 2;
    case MEDIA     = 3;
    case DIFICIL   = 4;

    public function etiqueta(): string
    {
        return match ($this) {
            self::MUY_FACIL => 'Muy fácil',
            self::FACIL     => 'Fácil',
            self::MEDIA     => 'Media',
            self::DIFICIL   => 'Difícil',
        };
    }
}
?>