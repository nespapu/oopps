<?php

declare(strict_types=1);

namespace Tests\Doubles\Exercise;

use App\Domain\Temas\SchoolContextRepository;

final class FakeSchoolContextRepository implements SchoolContextRepository
{
    public function __construct(
        private array $schoolContexts = []
    ) {
        if ($this->schoolContexts === []) {
            $this->schoolContexts = $this->defaultSchoolContexts();
        }
    }
    public function findByTopic(string $oppositionCode, int $topicOrder): array
    {
        return $this->schoolContexts;
    }

    private function defaultSchoolContexts(): array
    {
        return [
            [
                'schoolContextTeaching' => 'Teaching A',
                'schoolContextCycle' => 'Cycle A',
                'schoolContextModule' => 'Module A',
                'schoolContextConcept' => 'Concept A',
                'schoolContextApplication' => 'Application A',
                'schoolContextMethod' => 'Method A',
            ],
            [
                'schoolContextTeaching' => 'Teaching B',
                'schoolContextCycle' => 'Cycle B',
                'schoolContextModule' => 'Module B',
                'schoolContextConcept' => 'Concept B',
                'schoolContextApplication' => 'Application B',
                'schoolContextMethod' => 'Method B',
            ],
        ]; 
    }
}