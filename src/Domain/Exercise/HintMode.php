<?php

namespace App\Domain\Exercise;

enum HintMode: string
{
    case LETTERS = 'letras';
    case WORDS   = 'palabras';
}