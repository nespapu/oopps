<?php

namespace App\Application\Exercises\StepBuilder;

use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Temas\TemaRepository;

final class CuantoSabesTemaTituloPayloadBuilder {
    
    public function __construct(
        private TemaRepository $temaRepositorio,
        private HintService $pistaServicio
    ) {}

    public function construir(ExerciseSession $sesion) : array
    {
        $codigoOposicion = $sesion->userContext()->codigoOposicion();
        $numeracion = $sesion->config()->topicId();
        $titulo = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);
        $dificultadEnum = Difficulty::from($sesion->config()->difficulty());
        $modoPista = HintMode::WORDS;

        $pista = $titulo === null
            ? '(sin pista generada)'
            : $this->pistaServicio->getHint($titulo, $dificultadEnum, $modoPista);

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