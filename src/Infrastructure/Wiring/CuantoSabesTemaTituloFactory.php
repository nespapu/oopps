<?php

declare(strict_types=1);

namespace App\Infrastructure\Wiring;

use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySQL;
use App\Domain\Exercise\PistaService;

final class CuantoSabesTemaTituloFactory
{
    public function createPayloadBuilder(): CuantoSabesTemaTituloPayloadBuilder
    {
        $temaRepositorio = new TemaRepositorySQL();
        $pistaService = new PistaService();

        return new CuantoSabesTemaTituloPayloadBuilder($temaRepositorio, $pistaService);
    }
}
