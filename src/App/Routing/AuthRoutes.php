<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class AuthRoutes
{
    public function __construct(
        private readonly \Closure $showLoginHandler,
        private readonly \Closure $checkLoginHandler,
        private readonly \Closure $doLogoutHandler
    ) {}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: 'login',
            method: HttpMethod::GET,
            handler: $this->showLoginHandler
        ));

        $routes->add(new RouteDefinition(
            path: 'login',
            method: HttpMethod::POST,
            handler: $this->checkLoginHandler
        ));

        $routes->add(new RouteDefinition(
            path: 'login/salir',
            method: HttpMethod::POST,
            handler:$this->doLogoutHandler
        ));

        return $routes;
    }
}
