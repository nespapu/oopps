<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host,
        public readonly string $database,
        public readonly string $user,
        public readonly string $password,
        public readonly string $charset = 'utf8mb4',
    ) {}
}