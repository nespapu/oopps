<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Session\SessionStore;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseSession;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;

final class PhpExerciseSessionStore implements ExerciseSessionStore
{
    private const STORE_KEY = 'exercise_session_store_v2';

    public function __construct(
        private SessionStore $sessionStore
    ) {}

    public function create(
        ExerciseType $exerciseType,
        UserContext $userContext,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): ExerciseSession {
        $session = ExerciseSession::start($exerciseType, $userContext, $config, $firstStep);

        $this->save($session);
        $this->setCurrentSessionId($session->sessionId());

        return $session;
    }

    public function get(string $sessionId): ?ExerciseSession
    {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            return null;
        }

        $data = $this->readStore();
        $raw = $data['sessions'][$sessionId] ?? null;

        if (!is_array($raw)) {
            return null;
        }

        try {
            return $this->hydrate($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getCurrentSession(): ?ExerciseSession
    {
        $currentSessionId = $this->getCurrentSessionId();
        if ($currentSessionId === null) {
            return null;
        }

        return $this->get($currentSessionId);
    }

    public function save(ExerciseSession $session): void
    {
        $data = $this->readStore();
        $data['sessions'][$session->sessionId()] = $this->dehydrate($session);

        $this->writeStore($data);
    }

    public function delete(string $sessionId): void
    {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            return;
        }

        $data = $this->readStore();
        unset($data['sessions'][$sessionId]);

        if (($data['currentSessionId'] ?? null) === $sessionId) {
            $data['currentSessionId'] = null;
        }

        $this->writeStore($data);
    }

    public function setCurrentSessionId(string $sessionId): void
    {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            throw new \InvalidArgumentException('sessionId cannot be empty.');
        }

        $data = $this->readStore();
        $data['currentSessionId'] = $sessionId;

        $this->writeStore($data);
    }

    public function getCurrentSessionId(): ?string
    {
        $data = $this->readStore();
        $value = $data['currentSessionId'] ?? null;

        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        return $value;
    }

    public function clearCurrentSessionId(): void
    {
        $data = $this->readStore();
        $data['currentSessionId'] = null;

        $this->writeStore($data);
    }

    public function purgeExpiredSessions(int $ttlSeconds): int
    {
        if ($ttlSeconds <= 0) {
            return 0;
        }

        $data = $this->readStore();
        $sessions = $data['sessions'] ?? [];
        if (!is_array($sessions)) {
            return 0;
        }

        $now = new \DateTimeImmutable('now');
        $purged = 0;

        foreach ($sessions as $sessionId => $raw) {
            if (!is_array($raw)) {
                continue;
            }

            $updatedAtRaw = $raw['updatedAt'] ?? null;
            if (!is_string($updatedAtRaw) || $updatedAtRaw === '') {
                continue;
            }

            try {
                $updatedAt = new \DateTimeImmutable($updatedAtRaw);
            } catch (\Throwable) {
                continue;
            }

            $elapsed = $now->getTimestamp() - $updatedAt->getTimestamp();
            if ($elapsed > $ttlSeconds) {
                unset($sessions[$sessionId]);
                $purged++;
            }
        }

        $data['sessions'] = $sessions;

        $currentSessionId = $data['currentSessionId'] ?? null;
        if (is_string($currentSessionId) && $currentSessionId !== '' && !isset($sessions[$currentSessionId])) {
            $data['currentSessionId'] = null;
        }

        $this->writeStore($data);

        return $purged;
    }

    /**
     * @return array{sessions: array<string, array<string, mixed>>, currentSessionId: string|null}
     */
    private function readStore(): array
    {
        $raw = $this->sessionStore->getString(self::STORE_KEY);
        if ($raw === null || trim($raw) === '') {
            return ['sessions' => [], 'currentSessionId' => null];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return ['sessions' => [], 'currentSessionId' => null];
        }

        if (!is_array($decoded)) {
            return ['sessions' => [], 'currentSessionId' => null];
        }

        $sessions = $decoded['sessions'] ?? [];
        $currentSessionId = $decoded['currentSessionId'] ?? null;

        return [
            'sessions' => is_array($sessions) ? $sessions : [],
            'currentSessionId' => is_string($currentSessionId) && trim($currentSessionId) !== '' ? $currentSessionId : null,
        ];
    }

    /**
     * @param array{sessions: array<string, array<string, mixed>>, currentSessionId: string|null} $data
     */
    private function writeStore(array $data): void
    {
        $payload = json_encode($data, JSON_THROW_ON_ERROR);
        $this->sessionStore->setString(self::STORE_KEY, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function dehydrate(ExerciseSession $session): array
    {
        return [
            'sessionId' => $session->sessionId(),
            'exerciseType' => [
                'slug' => $session->exerciseType()->slug(),
                'name' => $session->exerciseType()->name(),
            ],
            'userContext' => [
                'username' => $session->userContext()->user(),
                'oppositionCode' => $session->userContext()->oppositionCode(),
            ],
            'config' => [
                'topicId' => $session->config()->topicId(),
                'difficulty' => $session->config()->difficulty(),
                'flags' => $session->config()->flags(),
            ],
            'currentStep' => $session->currentStep()->value,
            // these are intentionally raw; keep DTO-ish, not domain objects
            'answersByStep' => $this->safeArray($this->readPrivateProperty($session, 'answersByStep')),
            'evaluationByStep' => $this->safeArray($this->readPrivateProperty($session, 'evaluationByStep')),
            'createdAt' => $session->createdAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $session->updatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param array<string, mixed> $raw
     */
    private function hydrate(array $raw): ExerciseSession
    {
        $sessionId = (string) ($raw['sessionId'] ?? '');

        $exerciseTypeRaw = $raw['exerciseType'] ?? [];
        $typeSlug = is_array($exerciseTypeRaw) ? (string) ($exerciseTypeRaw['slug'] ?? '') : '';
        $exerciseType = ExerciseType::fromSlug($typeSlug);

        $userContextRaw = $raw['userContext'] ?? [];
        $userContextRaw = [
            'username' => is_array($userContextRaw) ? (string) ($userContextRaw['username'] ?? '') : '',
            'oppositionCode' => is_array($userContextRaw) ? (string) ($userContextRaw['oppositionCode'] ?? '') : '',
        ];
        $userContext = new UserContext($userContextRaw['username'], $userContextRaw['oppositionCode']);

        $configRaw = $raw['config'] ?? [];
        $topicId = is_array($configRaw) ? (int) ($configRaw['topicId'] ?? 0) : 0;
        $difficulty = is_array($configRaw) ? (int) ($configRaw['difficulty'] ?? 1) : 1;
        $flags = is_array($configRaw) ? ($configRaw['flags'] ?? []) : [];
        if (!is_array($flags)) {
            $flags = [];
        }

        $config = new ExerciseConfig($topicId, $difficulty, $flags);

        $rawStep = (string) ($raw['currentStep'] ?? ExerciseStep::first()->value);
        $currentStep = ExerciseStep::from($rawStep);

        $createdAt = new \DateTimeImmutable((string) ($raw['createdAt'] ?? 'now'));
        $updatedAt = new \DateTimeImmutable((string) ($raw['updatedAt'] ?? 'now'));

        $answersByStep = $raw['answersByStep'] ?? [];
        $evaluationByStep = $raw['evaluationByStep'] ?? [];

        if (!is_array($answersByStep)) {
            $answersByStep = [];
        }
        if (!is_array($evaluationByStep)) {
            $evaluationByStep = [];
        }

        return new ExerciseSession(
            $sessionId,
            $exerciseType,
            $userContext,
            $config,
            $currentStep,
            $createdAt,
            $updatedAt,
            $answersByStep,
            $evaluationByStep
        );
    }

    /**
     * @param mixed $value
     * @return array<string, mixed>
     */
    private function safeArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * Escape hatch: since ExerciseSession keeps these arrays private,
     * we read them via reflection to avoid changing your domain model right now.
     * If you prefer, we can instead add public getters in ExerciseSession and delete this.
     */
    private function readPrivateProperty(object $object, string $property): mixed
    {
        $ref = new \ReflectionClass($object);
        if (!$ref->hasProperty($property)) {
            return null;
        }

        $prop = $ref->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($object);
    }
}