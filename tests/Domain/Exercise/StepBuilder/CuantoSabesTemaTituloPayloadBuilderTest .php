<?php

declare(strict_types=1);

namespace Tests\Domain\Exercise\StepBuilder;

use PHPUnit\Framework\TestCase;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Domain\Auth\ContextoUsuario;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use Tests\Domain\Exercise\Doubles\FakeTemaRepository;

final class CuantoSabesTemaTituloPayloadBuilderTest extends TestCase
{
    public function testConstruyeElPayloadConPistaYMetadatos(): void
    {
        $repo = new FakeTemaRepository('Sistemas operativos. Gestión de procesos', null);
        $pistaServicio = new HintService();

        $builder = new CuantoSabesTemaTituloPayloadBuilder($repo, $pistaServicio);

        $session = $this->createMock(ExerciseSession::class);

        $session->method('contextoUsuario')
                ->willReturn($this->contextoUsuarioDummy());

        $config = new class {
            public function tema(): int { return 16; }
            public function dificultad(): int { return 2; } 
            public function banderas(): array { return []; }
        };

        $session->method('config')->willReturn($config);

        $payload = $builder->construir($session);

        $this->assertSame(ExerciseStep::TITLE, $payload[ClavesPasoPayload::PASO]);

        $this->assertArrayHasKey(ClavesPasoPayload::ITEMS, $payload);
        $this->assertSame('text', $payload[ClavesPasoPayload::ITEMS][0]['tipo']);
        $this->assertSame('titulo', $payload[ClavesPasoPayload::ITEMS][0]['nombre']);

        $this->assertArrayHasKey('pista', $payload[ClavesPasoPayload::ITEMS][0]);
        $this->assertNotSame('', $payload[ClavesPasoPayload::ITEMS][0]['pista']);

        $this->assertSame(16, $payload[ClavesPasoPayload::META]['numeracionTema']);
        $this->assertSame('Sistemas operativos. Gestión de procesos', $payload[ClavesPasoPayload::META]['tituloTema']);
        $this->assertSame(HintMode::WORDS->value, $payload[ClavesPasoPayload::META]['tipoPista']);
    }

    public function testUsaPistaDeRespaldoSiNoHayTitulo(): void
    {
        $repo = new FakeTemaRepository(null, null);
        $pistaServicio = new HintService();

        $builder = new CuantoSabesTemaTituloPayloadBuilder($repo, $pistaServicio);

        $session = $this->createMock(ExerciseSession::class);
        $session->method('contextoUsuario')
                ->willReturn($this->contextoUsuarioDummy());

        $config = new class {
            public function tema(): int { return 16; }
            public function dificultad(): int { return 2; } 
            public function banderas(): array { return []; }
        };
        $session->method('config')->willReturn($config);

        $payload = $builder->construir($session);

        $this->assertSame('(sin pista generada)', $payload[ClavesPasoPayload::ITEMS][0]['pista']);
    }

    private function contextoUsuarioDummy(): ContextoUsuario
    {
        return new ContextoUsuario(
            'nestor',
            '590107'
        );
    }
}
