<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class DevRoutes
{
    public function __construct(
        private readonly DevPaths $devPaths,
        private readonly \Closure $showHandler,
        private readonly \Closure $nextHandler,
        private readonly \Closure $resetHandler
    ){}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: $this->devPaths->base(),
            method: HttpMethod::GET,
            handler: $this->showHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->devPaths->siguiente(),
            method: HttpMethod::POST,
            handler: $this->nextHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->devPaths->reset(),
            method: HttpMethod::POST,
            handler: $this->resetHandler
        ));

        return $routes;
    }
}