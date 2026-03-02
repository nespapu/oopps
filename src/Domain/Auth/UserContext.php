<?php

declare(strict_types=1);

namespace App\Domain\Auth;

final class UserContext
{
    private readonly string $user;
    private readonly string $oppositionCode;

    public function __construct(string $user, string $oppositionCode)
    {
        $this->user = trim($user);
        $this->oppositionCode = trim($oppositionCode);

        if ($this->user === '') {
            throw new \InvalidArgumentException('User cannot be empty');
        }
        if ($this->oppositionCode === '') {
            throw new \InvalidArgumentException('Opposition code cannot be empty');
        }
    }

    public function user(): string
    {
        return $this->user;
    }

    public function oppositionCode(): string
    {
        return $this->oppositionCode;
    }
}
