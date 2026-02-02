<?php

namespace App\Application\Exercises\StepBuilder;

use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Domain\Exercise\Dificultad;
use App\Domain\Exercise\ModoPista;
use App\Domain\Exercise\PasoEjercicio;
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
        $numeracion = $sesion->config()->tema();
        $titulo = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);
        $dificultadEnum = Dificultad::from($sesion->config()->dificultad());
        $modoPista = ModoPista::PALABRAS;

        $pista = $titulo === null
            ? '(sin pista generada)'
            : $this->pistaServicio->getPista($titulo, $dificultadEnum, $modoPista);

        return [
            ClavesPasoPayload::PASO => PasoEjercicio::TITULO,
            ClavesPasoPayload::ITEMS => [
                [
                    "tipo" => "text",
                    "nombre" => "titulo",
                    "pista" => $pista,
                    "placeholder" => "Escribe el título del tema"
                ]
            ],
            ClavesPasoPayload::META => [
                "numeracionTema" => $numeracion,
                "tituloTema" => $titulo,
                "gradoDificultad" => $sesion->config()->dificultad(),
                "banderas" => $sesion->config()->banderas(),
                "tipoPista" => $modoPista->value
            ],
            ClavesPasoPayload::ESPERADO => []
        ];
    }

}
?>