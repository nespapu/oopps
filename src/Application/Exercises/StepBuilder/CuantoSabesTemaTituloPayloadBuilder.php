<?php

namespace App\Application\Exercises\StepBuilder;

use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Domain\Exercise\Dificultad;
use App\Domain\Exercise\ModoPista;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\PistaService;
use App\Domain\Exercise\SesionEjercicio;
use App\Infrastructure\Persistence\Repositories\TemaRepositorio;

final class CuantoSabesTemaTituloPayloadBuilder {
    
    public function __construct(
        private TemaRepositorio $temaRepositorio
    ) {}

    public function construir(SesionEjercicio $sesion) : array
    {
        $codigoOposicion = $sesion->contextoUsuario()['oposicionId'];
        $numeracion = $sesion->config()->tema();
        $titulo = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);

        $servicioPista = new PistaService();
        $dificultadEnum = Dificultad::from($sesion->config()->dificultad());
        $modoPista = ModoPista::PALABRAS;

        $pista = $servicioPista->getPista($titulo, $dificultadEnum, $modoPista);
        if ($pista === null || $pista === '') {
            $pista = '(sin pista generada)'; // temporal
        }

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