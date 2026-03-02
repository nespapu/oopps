<?php

namespace App\Application\Exercises;

use App\Domain\Auth\UserContext;
use App\Domain\Exercise\Difficulty;
use App\Domain\Temas\TemaRepository;

final class CuantoSabesTemaConfigPayloadBuilder
{
    public function __construct(
        private TemaRepository $temaRepositorio
    ) {}

    public function construir(UserContext $ctx): array
    {
        return [
            'temas' => $this->contruirOpcionesTema($ctx),
            'gradosDificultad' => $this->construirOpcionesDificultad(),
            'defecto' => [
                'tema' => 0,
                'gradoDificultad' => Difficulty::MEDIUM->value,
            ],
        ];
    }

    private function contruirOpcionesTema(UserContext $ctx): array
    {
        $temas = $this->temaRepositorio->buscarPorCodigoOposicion($ctx->oppositionCode());

        $opciones = array_map(
            fn(array $fila) => [
                'valor' => (int)$fila['numeracion'],       
                'etiqueta' => $fila['titulo'],
            ],
            $temas
        );

        // Añadir la opción aleatorio
        array_unshift($opciones, [
            'valor' => 0,
            'etiqueta' => 'Aleatorio',
        ]);

        return $opciones;
    }

    private function construirOpcionesDificultad(): array
    {
        return array_map(
            static fn(Difficulty $d) => [
                'valor' => $d->value,
                'etiqueta' => $d->label(),
            ],
            Difficulty::cases()
        );
    }
}
