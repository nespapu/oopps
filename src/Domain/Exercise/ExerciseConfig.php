<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class ExerciseConfig
{
    private int $topicId; // 0 = random
    private int $difficulty; // 1..4
    /** @var array<string,bool> */
    private array $flags;

    /**
     * @param array<string,bool|mixed> $flags
     */
    public function __construct(int $topicId, int $difficulty, array $flags = [])
    {
        if ($topicId < 0) {
            throw new \InvalidArgumentException('Topic must be >= 0 (0 means random).');
        }
        if ($difficulty < 1 || $difficulty > 4) {
            throw new \InvalidArgumentException('Difficulty must be between 1 and 4.');
        }

        $normalizedFlags = [];
        foreach ($flags as $key => $value) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }
            $normalizedFlags[$key] = (bool) $value;
        }

        $this->topicId = $topicId;
        $this->difficulty = $difficulty;
        $this->flags = $normalizedFlags;
    }

    public function topicId(): int
    {
        return $this->topicId;
    }

    public function isRandomTopic(): bool
    {
        return $this->topicId === 0;
    }

    public function difficulty(): int
    {
        return $this->difficulty;
    }

    /**
     * @return array<string,bool>
     */
    public function flags(): array
    {
        return $this->flags;
    }

    public function isFlagEnabled(string $flagKey, bool $default = false): bool
    {
        $flagKey = trim($flagKey);
        if ($flagKey === '') {
            return $default;
        }

        return $this->flags[$flagKey] ?? $default;
    }

    public function withFlag(string $flagKey, bool $enabled): self
    {
        $flagKey = trim($flagKey);
        if ($flagKey === '') {
            return $this;
        }

        $flags = $this->flags;
        $flags[$flagKey] = $enabled;

        return new self($this->topicId, $this->difficulty, $flags);
    }
}