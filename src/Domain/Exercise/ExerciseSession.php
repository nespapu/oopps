<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

use App\Domain\Auth\ContextoUsuario;

final class ExerciseSession
{
    private string $sessionId;
    private ExerciseType $exerciseType;
    private ContextoUsuario $userContext;

    private ExerciseConfig $config;
    private ExerciseStep $currentStep;

    /** @var array<string, mixed> */
    private array $answersByStep;

    /** @var array<string, mixed> */
    private array $evaluationByStep;

    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $sessionId,
        ExerciseType $exerciseType,
        ContextoUsuario $userContext,
        ExerciseConfig $config,
        ExerciseStep $currentStep,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        array $answersByStep = [],
        array $evaluationByStep = []
    ) {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            throw new \InvalidArgumentException('sessionId cannot be empty.');
        }

        $this->sessionId = $sessionId;
        $this->exerciseType = $exerciseType;
        $this->userContext = $userContext;
        $this->config = $config;
        $this->currentStep = $currentStep;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->answersByStep = $answersByStep;
        $this->evaluationByStep = $evaluationByStep;
    }

    public static function start(
        ExerciseType $exerciseType,
        ContextoUsuario $userContext,
        ExerciseConfig $config,
        ExerciseStep $firstStep
    ): self {
        $now = new \DateTimeImmutable('now');

        return new self(
            self::generateSessionId(),
            $exerciseType,
            $userContext,
            $config,
            $firstStep,
            $now,
            $now
        );
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function exerciseType(): ExerciseType
    {
        return $this->exerciseType;
    }

    public function userContext(): ContextoUsuario
    {
        return $this->userContext;
    }

    public function config(): ExerciseConfig
    {
        return $this->config;
    }

    public function currentStep(): ExerciseStep
    {
        return $this->currentStep;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function moveToStep(ExerciseStep $step): void
    {
        $this->currentStep = $step;
        $this->touch();
    }

    /**
     * @param mixed $answerDto
     */
    public function setStepAnswer(ExerciseStep $step, mixed $answerDto): void
    {
        $this->answersByStep[$step->value] = $answerDto;
        $this->touch();
    }

    /**
     * @param mixed $evaluationDto
     */
    public function setStepEvaluation(ExerciseStep $step, mixed $evaluationDto): void
    {
        $this->evaluationByStep[$step->value] = $evaluationDto;
        $this->touch();
    }

    public function getStepAnswer(ExerciseStep $step): mixed
    {
        return $this->answersByStep[$step->value] ?? null;
    }

    public function getStepEvaluation(ExerciseStep $step): mixed
    {
        return $this->evaluationByStep[$step->value] ?? null;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    private static function generateSessionId(): string
    {
        // 32 hex chars
        return bin2hex(random_bytes(16));
    }
}