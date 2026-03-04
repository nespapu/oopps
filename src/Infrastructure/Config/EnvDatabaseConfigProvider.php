<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class EnvDatabaseConfigProvider implements DatabaseConfigProvider
{
    public function get(): DatabaseConfig
    {
        $host = $this->env('DB_HOST');
        $db   = $this->env('DB_NAME');
        $user = $this->env('DB_USER');
        $pass = $this->env('DB_PASS');

        return new DatabaseConfig(
            host: $host,
            database: $db,
            user: $user,
            password: $pass
        );
    }

    private function env(string $key): string
    {
        $value = $_ENV[$key] ?? null;

        if (!is_string($value) || $value === '') {
            throw new \RuntimeException("Missing or empty env var: {$key}");
        }

        return $value;
    }
}