<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class PanelControlEjerciciosRoutes
{
    public function __construct(
        private readonly PanelControlEjerciciosPaths $panelControlEjerciciosPaths,
        private readonly \Closure $showPanelHandler
    ){}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: $this->panelControlEjerciciosPaths->panel(),
            method: HttpMethod::GET,
            handler: $this->showPanelHandler
        ));

        return $routes;
    }
}