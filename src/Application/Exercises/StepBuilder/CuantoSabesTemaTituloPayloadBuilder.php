<?php

namespace App\Application\Exercises\StepBuilder;

use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\ModoPista;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\PistaService;
use App\Domain\Exercise\SesionEjercicio;
use App\Domain\Temas\TemaRepository;

final class CuantoSabesTemaTituloPayloadBuilder {
    
    public function __construct(
        private TemaRepository $temaRepositorio,
        private PistaService $pistaServicio
    ) {}

    public function construir(SesionEjercicio $sesion) : array
    {
        $codigoOposicion = $sesion->contextoUsuario()->codigoOposicion();
        $numeracion = $sesion->config()->topicId();
        $titulo = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);
        $dificultadEnum = Difficulty::from($sesion->config()->difficulty());
        $modoPista = ModoPista::PALABRAS;

        $pista = $titulo === null
            ? '(sin pista generada)'
            : $this->pistaServicio->getPista($titulo, $dificultadEnum, $modoPista);

        return [
            "step" => ExerciseStep::TITLE->value,
            ClavesPasoPayload::ITEMS => [
                [
                    "key" => "titulo",
                    "tipo" => "text",
                    "nombre" => "titulo",
                    "pista" => $pista,
                    "placeholder" => "Escribe el título del tema"
                ]
            ],
            ClavesPasoPayload::META => [
                "numeracionTema" => $numeracion,
                "tituloTema" => $titulo,
                "gradoDificultad" => $sesion->config()->difficulty(),
                "banderas" => $sesion->config()->flags(),
                "tipoPista" => $modoPista->value
            ],
            "expected" => [
                "titulo" => $titulo
            ]
        ];
    }

}
?>