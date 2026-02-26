<?php

namespace App\Domain\Exercise;

enum Difficulty: int
{
    case VERY_EASY = 1;
    case EASY      = 2;
    case MEDIUM    = 3;
    case HARD      = 4;

    public function label(): string
    {
        return match ($this) {
            self::VERY_EASY => 'Muy fácil',
            self::EASY      => 'Fácil',
            self::MEDIUM    => 'Media',
            self::HARD      => 'Difícil',
        };
    }
}