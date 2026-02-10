<?php
declare(strict_types=1);

namespace App\App\Routing;

use App\Application\Routing\RouteUrlGenerator;

final class CuantoSabesTemaPaths
{
    private const CONFIG = 'ejercicios/cuanto-sabes-tema/config';
    private const INICIO  = 'ejercicios/cuanto-sabes-tema/inicio';

    private const PASO_TITULO = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo';
    private const EVAL_TITULO = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/titulo/evaluar';

    private const PASO_INDICE = 'ejercicios/cuanto-sabes-tema/sesiones/{sesionId}/pasos/indice';

    public function __construct (
        private readonly RouteUrlGenerator $routeUrlGenerator
    ){}

    public function config(): string 
    { 
        return self::CONFIG; 
    }

    public function inicio(): string
    {
        return self::INICIO;
    }

    public function pasoTituloPattern(): string
    {
        return self::PASO_TITULO;
    }

    public function pasoTitulo(string $sesionId): string
    {
        return $this->routeUrlGenerator->generate(self::PASO_TITULO, [
            'sesionId' => $sesionId,
        ]);
    }

    public function pasoIndicePattern(): string
    {
        return self::PASO_INDICE;
    }

    public function pasoIndice(string $sesionId): string
    {
        return $this->routeUrlGenerator->generate(self::PASO_INDICE, [
            'sesionId' => $sesionId,
        ]);
    }

    public function evaluarTituloPattern(): string
    {
        return self::EVAL_TITULO;
    }

    public function evaluarTitulo(string $sesionId): string
    {
        return $this->routeUrlGenerator->generate(self::EVAL_TITULO, [
            'sesionId' => $sesionId,
        ]);
    }
}
