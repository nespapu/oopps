<?php

declare(strict_types=1);

namespace App\App\Routing;

use App\Application\Routing\RouteUrlGenerator;

final class HowMuchDoYouKnowPaths
{
    private const CONFIG = 'ejercicios/cuanto-sabes-tema/config';
    private const START  = 'ejercicios/cuanto-sabes-tema/inicio';

    private const TITLE_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo';
    private const TITLE_EVALUATION = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo/evaluar';

    private const INDEX_STEP = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/indice';

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
}