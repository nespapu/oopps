<?php

declare(strict_types=1);

namespace App\Application\Exercises;

use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;

interface ExerciseSessionStore
{
    public function create(
        ExerciseType $exerciseType,
        UserContext $userContext,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): ExerciseSession;

    public function get(string $sessionId): ?ExerciseSession;

    public function getCurrentSession(): ?ExerciseSession;

    public function save(ExerciseSession $session): void;

    public function delete(string $sessionId): void;

    public function setCurrentSessionId(string $sessionId): void;

    public function getCurrentSessionId(): ?string;

    public function clearCurrentSessionId(): void;

    public function purgeExpiredSessions(int $ttlSeconds): int;
}
