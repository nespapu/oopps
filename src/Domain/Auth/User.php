<?php

declare(strict_types=1);

namespace App\Domain\Auth;

final class User
{
    public function __construct(
        private string $name,
        private string $password,
        private string $oppositionCode
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function oppositionCode(): string
    {
        return $this->oppositionCode;
    }
}
