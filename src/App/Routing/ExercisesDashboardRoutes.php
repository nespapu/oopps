<?php

declare(strict_types=1);

namespace App\App\Routing;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class ExercisesDashboardRoutes
{
    public function __construct(
        private readonly ExercisesDashboardPaths $paths,
        private readonly \Closure $showDashboardHandler
    ) {}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: $this->paths->dashboard(),
            method: HttpMethod::GET,
            handler: $this->showDashboardHandler
        ));

        return $routes;
    }
}