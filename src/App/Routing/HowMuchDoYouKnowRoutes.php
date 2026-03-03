<?php

declare(strict_types=1);

namespace App\App\Routing;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class HowMuchDoYouKnowRoutes
{
    public function __construct(
        private readonly HowMuchDoYouKnowPaths $paths,
        private readonly \Closure $showConfigHandler,
        private readonly \Closure $checkConfigHandler,
        private readonly \Closure $showTitleStepHandler,
        private readonly \Closure $evaluateTitleStepHandler,
        private readonly \Closure $showIndexStepHandler
    ) {}

    public function routes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add(new RouteDefinition(
            path: $this->paths->config(),
            method: HttpMethod::GET,
            handler: $this->showConfigHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->start(),
            method: HttpMethod::POST,
            handler: $this->checkConfigHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->titleStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showTitleStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->titleEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateTitleStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->indexStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showIndexStepHandler
        ));

        return $routes;
    }
}