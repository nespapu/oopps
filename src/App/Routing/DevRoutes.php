<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Core\Routes\Dev\RutasDevSesionEjercicio;
use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class DevRoutes
{
    public function __construct(
        private readonly \Closure $showHandler,
        private readonly \Closure $nextHandler,
        private readonly \Closure $resetHandler
    ){}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: RutasDevSesionEjercicio::BASE,
            method: HttpMethod::GET,
            handler: $this->showHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasDevSesionEjercicio::SIGUIENTE,
            method: HttpMethod::POST,
            handler: $this->nextHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasDevSesionEjercicio::RESET,
            method: HttpMethod::POST,
            handler: $this->resetHandler
        ));

        return $routes;
    }
}