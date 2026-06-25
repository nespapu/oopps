<?php

namespace App\Application\Exercises\Evaluation;

enum EvaluationMode: string
{
    case EQUALITY = 'equality';
    case SIMILARITY = 'similarity';
}