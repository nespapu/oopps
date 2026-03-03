<?php

declare(strict_types=1);

namespace App\App\Routing;

final class ExercisesDashboardPaths
{
    private const DASHBOARD = 'panel-control-ejercicios';

    public function dashboard(): string
    {
        return self::DASHBOARD;
    }
}