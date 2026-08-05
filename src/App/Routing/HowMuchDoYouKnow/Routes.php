<?php

declare(strict_types=1);

namespace App\App\Routing\HowMuchDoYouKnow;

use App\Infrastructure\Routing\HttpMethod;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\RouteDefinition;

final class Routes
{
    public function __construct(
        private readonly Paths $paths,
        private readonly \Closure $showConfigHandler,
        private readonly \Closure $checkConfigHandler,
        private readonly \Closure $showTitleStepHandler,
        private readonly \Closure $evaluateTitleStepHandler,
        private readonly \Closure $showIndexStepHandler,
        private readonly \Closure $evaluateIndexStepHandler,
        private readonly \Closure $showJustificationStepHandler,
        private readonly \Closure $evaluateJustificationStepHandler,
        private readonly \Closure $showQuotesStepHandler,
        private readonly \Closure $evaluateQuotesStepHandler,
        private readonly \Closure $showToolsStepHandler,
        private readonly \Closure $evaluateToolsStepHandler,
        private readonly \Closure $showSchoolContextStepHandler,
        private readonly \Closure $evaluateSchoolContextStepHandler,
        private readonly \Closure $showWorkContextStepHandler,
        private readonly \Closure $evaluateWorkContextStepHandler,
        private readonly \Closure $showBibliographyStepHandler,
        private readonly \Closure $evaluateBibliographyStepHandler,
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

        $routes->add(new RouteDefinition(
            path: $this->paths->indexEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateIndexStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->justificationStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showJustificationStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->justificationEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateJustificationStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->quotesStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showQuotesStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->quotesEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateQuotesStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->toolsStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showToolsStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->toolsEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateToolsStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->schoolContextStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showSchoolContextStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->schoolContextEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateSchoolContextStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->workContextStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showWorkContextStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->workContextEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateWorkContextStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->bibliographyStepPattern(),
            method: HttpMethod::GET,
            handler: $this->showBibliographyStepHandler
        ));

        $routes->add(new RouteDefinition(
            path: $this->paths->bibliographyEvaluationPattern(),
            method: HttpMethod::POST,
            handler: $this->evaluateBibliographyStepHandler
        ));

        return $routes;
    }
}