<?php

declare(strict_types=1);

namespace Tests\Domain\Exercise\StepBuilder;

use PHPUnit\Framework\TestCase;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowTitlePayloadBuilder;
use App\Application\Exercises\Payload\StepPayloadKeys;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use Tests\Domain\Exercise\Doubles\FakeTopicRepository;

final class CuantoSabesTemaTituloPayloadBuilderTest extends TestCase
{
    public function testConstruyeElPayloadConPistaYMetadatos(): void
    {
        $repo = new FakeTopicRepository('Sistemas operativos. Gestión de procesos', null);
        $pistaServicio = new HintService();

        $builder = new HowMuchDoYouKnowTitlePayloadBuilder($repo, $pistaServicio);

        $session = $this->createMock(ExerciseSession::class);

        $session->method('contextoUsuario')
                ->willReturn($this->contextoUsuarioDummy());

        $config = new class {
            public function tema(): int { return 16; }
            public function dificultad(): int { return 2; } 
            public function banderas(): array { return []; }
        };

        $session->method('config')->willReturn($config);

        $payload = $builder->build($session);

        $this->assertSame(ExerciseStep::TITLE, $payload[StepPayloadKeys::STEP]);

        $this->assertArrayHasKey(StepPayloadKeys::ITEMS, $payload);
        $this->assertSame('text', $payload[StepPayloadKeys::ITEMS][0]['tipo']);
        $this->assertSame('titulo', $payload[StepPayloadKeys::ITEMS][0]['nombre']);

        $this->assertArrayHasKey('pista', $payload[StepPayloadKeys::ITEMS][0]);
        $this->assertNotSame('', $payload[StepPayloadKeys::ITEMS][0]['pista']);

        $this->assertSame(16, $payload[StepPayloadKeys::META]['numeracionTema']);
        $this->assertSame('Sistemas operativos. Gestión de procesos', $payload[StepPayloadKeys::META]['tituloTema']);
        $this->assertSame(HintMode::WORDS->value, $payload[StepPayloadKeys::META]['tipoPista']);
    }

    public function testUsaPistaDeRespaldoSiNoHayTitulo(): void
    {
        $repo = new FakeTopicRepository(null, null);
        $pistaServicio = new HintService();

        $builder = new HowMuchDoYouKnowTitlePayloadBuilder($repo, $pistaServicio);

        $session = $this->createMock(ExerciseSession::class);
        $session->method('contextoUsuario')
                ->willReturn($this->contextoUsuarioDummy());

        $config = new class {
            public function tema(): int { return 16; }
            public function dificultad(): int { return 2; } 
            public function banderas(): array { return []; }
        };
        $session->method('config')->willReturn($config);

        $payload = $builder->build($session);

        $this->assertSame('(sin pista generada)', $payload[StepPayloadKeys::ITEMS][0]['pista']);
    }

    private function contextoUsuarioDummy(): UserContext
    {
        return new UserContext(
            'nestor',
            '590107'
        );
    }
}
