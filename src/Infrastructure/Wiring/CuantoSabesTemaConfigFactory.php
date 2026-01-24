<?php

declare(strict_types=1);

namespace App\Infrastructure\Wiring;

use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySql;

final class CuantoSabesTemaConfigFactory
{
    public function createPayloadBuilder(): CuantoSabesTemaConfigPayloadBuilder
    {
        $temaRepository = new TemaRepositorySql();

        return new CuantoSabesTemaConfigPayloadBuilder($temaRepository);
    }
}
?>