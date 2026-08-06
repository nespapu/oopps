<?php

declare(strict_types=1);

namespace App\App\Routing\HowMuchDoYouKnow;

use App\Application\Routing\RouteUrlGenerator;

final class Paths
{
    private const CONFIG = 'ejercicios/cuanto-sabes-tema/config';
    private const START  = 'ejercicios/cuanto-sabes-tema/inicio';

    private const TITLE_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo';
    private const TITLE_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo/evaluar';

    private const INDEX_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/indice';
    private const INDEX_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/indice/evaluar';

    private const JUSTIFICATION_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/justificacion';
    private const JUSTIFICATION_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/justificacion/evaluar';

    private const QUOTES_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/citas';
    private const QUOTES_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/citas/evaluar';

    private const TOOLS_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/herramientas';
    private const TOOLS_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/herramientas/evaluar';

    private const SCHOOL_CONTEXT_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/contexto-escolar';
    private const SCHOOL_CONTEXT_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/contexto-escolar/evaluar';

    private const WORK_CONTEXT_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/contexto-laboral';
    private const WORK_CONTEXT_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/contexto-laboral/evaluar';

    private const BIBLIOGRAPHY_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/bibliografia';
    private const BIBLIOGRAPHY_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/bibliografia/evaluar';

    private const WEBGRAPHY_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/webgrafia';
    private const WEBGRAPHY_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/webgrafia/evaluar';

    private const RESULT_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/resultado';

    public function __construct(
        private readonly RouteUrlGenerator $routeUrlGenerator
    ) {}

    public function config(): string
    {
        return self::CONFIG;
    }

    public function start(): string
    {
        return self::START;
    }

    public function titleStepPattern(): string
    {
        return self::TITLE_STEP;
    }

    public function titleStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::TITLE_STEP, [
            'sesionId' => $sessionId, // keep legacy placeholder name
        ]);
    }

    public function indexStepPattern(): string
    {
        return self::INDEX_STEP;
    }

    public function indexStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::INDEX_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function justificationStepPattern(): string
    {
        return self::JUSTIFICATION_STEP;
    }

    public function justificationStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::JUSTIFICATION_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function quotesStepPattern(): string
    {
        return self::QUOTES_STEP;
    }

    public function quotesStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::QUOTES_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function toolsStepPattern(): string
    {
        return self::TOOLS_STEP;
    }


    public function toolsStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::TOOLS_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function schoolContextStepPattern(): string
    {
        return self::SCHOOL_CONTEXT_STEP;
    }

    public function schoolContextStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::SCHOOL_CONTEXT_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function workContextStepPattern(): string
    {
        return self::WORK_CONTEXT_STEP;
    }


    public function workContextStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::WORK_CONTEXT_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function bibliographyStepPattern(): string
    {
        return self::BIBLIOGRAPHY_STEP;
    }

    public function bibliographyStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::BIBLIOGRAPHY_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function webgraphyStepPattern(): string
    {
        return self::WEBGRAPHY_STEP;
    }


    public function webgraphyStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::WEBGRAPHY_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function resultStepPattern(): string
    {
        return self::RESULT_STEP;
    }

    public function resultStep(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::RESULT_STEP, [
            'sesionId' => $sessionId,
        ]);
    }

    public function titleEvaluationPattern(): string
    {
        return self::TITLE_EVALUATION;
    }

    public function titleEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::TITLE_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function indexEvaluationPattern(): string
    {
        return self::INDEX_EVALUATION;
    }

    public function indexEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::INDEX_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function justificationEvaluationPattern(): string
    {
        return self::JUSTIFICATION_EVALUATION;
    }


    public function justificationEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::JUSTIFICATION_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function quotesEvaluationPattern(): string
    {
        return self::QUOTES_EVALUATION;
    }

    public function quotesEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::QUOTES_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function toolsEvaluationPattern(): string
    {
        return self::TOOLS_EVALUATION;
    }


    public function toolsEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::TOOLS_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function schoolContextEvaluationPattern(): string
    {
        return self::SCHOOL_CONTEXT_EVALUATION;
    }

    public function schoolContextEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::SCHOOL_CONTEXT_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function workContextEvaluationPattern(): string
    {
        return self::WORK_CONTEXT_EVALUATION;
    }

    public function workContextEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::WORK_CONTEXT_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function bibliographyEvaluationPattern(): string
    {
        return self::BIBLIOGRAPHY_EVALUATION;
    }

    public function bibliographyEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::BIBLIOGRAPHY_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }

    public function webgraphyEvaluationPattern(): string
    {
        return self::WEBGRAPHY_EVALUATION;
    }

    public function webgraphyEvaluation(string $sessionId): string
    {
        return $this->routeUrlGenerator->generate(self::WEBGRAPHY_EVALUATION, [
            'sesionId' => $sessionId,
        ]);
    }
}