<?php

declare(strict_types=1);

namespace tests\Domain\Exercise\Doubles;

use App\Domain\Temas\TemaRepository;

final class FakeTemaRepository implements TemaRepository
{
    public function __construct(
        private ?string $title,
        private ?int $orden
    ) {}

    public function buscarPorCodigoOposicion(string $codigoOposicion): array
    {
        return [
            ['numeracion' => 16, 'titulo' => 'Sistemas operativos. Gestión de procesos'],
        ];
    }

    public function buscarTituloPorCodigoOposicionYOrden(string $codigoOposicion, int $orden): ?string
    {
        return $this->title;
    }

    public function buscarOrdenAleatorioPorCodigoOposicion(string $codigoOposicion): ?int
    {
        return $this->orden;
    }
}
?>
