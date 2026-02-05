<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Core\Routes\RutasCuantoSabesTema;
use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class CuantoSabesTemaRoutes
{
    public function __construct(
        private readonly \Closure $showConfigHandler,
        private readonly \Closure $checkConfigHandler,
        private readonly \Closure $showTitleStepHandler,
        private readonly \Closure $evaluateTitleStepHandler,
        private readonly \Closure $showIndexStepHandler
    ){}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: RutasCuantoSabesTema::CONFIG,
            method: HttpMethod::GET,
            handler: $this->showConfigHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasCuantoSabesTema::INICIO,
            method: HttpMethod::POST,
            handler: $this->checkConfigHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasCuantoSabesTema::PASO_TITULO,
            method: HttpMethod::GET,
            handler: $this->showTitleStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasCuantoSabesTema::EVAL_TITULO,
            method: HttpMethod::POST,
            handler: $this->evaluateTitleStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: RutasCuantoSabesTema::PASO_INDICE,
            method: HttpMethod::GET,
            handler: $this->showIndexStepHandler
        ));

        return $routes;
    }
}