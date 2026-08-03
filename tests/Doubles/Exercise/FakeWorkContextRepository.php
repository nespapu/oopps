<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\WorkContextRepository;


final class FakeWorkContextRepository implements WorkContextRepository
{
    public function __construct(
        private array $workContexts = []
    ) {
        if ($this->workContexts === []) {
            $this->workContexts = $this->defaultWorkContexts();
        }
    }
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return $this->workContexts;
    }


    private function defaultWorkContexts(): array
    {
        return [
            [
                'workContextField' => 'Field A',
                'workContextRole' => 'Role A',
                'workContextConcept' => 'Concept A',
                'workContextApplication' => 'Application A',
                'workContextBenefit' => 'Benefit A',
            ],
            [
                'workContextField' => 'Field B',
                'workContextRole' => 'Role B',
                'workContextConcept' => 'Concept B',
                'workContextApplication' => 'Application B',
                'workContextBenefit' => 'Benefit B',
            ],
        ]; 
    }
}