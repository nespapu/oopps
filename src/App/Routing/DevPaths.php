<?php

declare(strict_types=1);

namespace App\App\Routing;

final class DevPaths
{
    private const EXERCISE_SESSION_BASE = 'dev/sesion-ejercicio';
    private const EXERCISE_SESSION_NEXT = 'dev/sesion-ejercicio/siguiente';
    private const EXERCISE_SESSION_RESET = 'dev/sesion-ejercicio/reset';

    public function exerciseSessionBase(): string
    {
        return self::EXERCISE_SESSION_BASE;
    }

    public function exerciseSessionNext(): string
    {
        return self::EXERCISE_SESSION_NEXT;
    }

    public function exerciseSessionReset(): string
    {
        return self::EXERCISE_SESSION_RESET;
    }
}