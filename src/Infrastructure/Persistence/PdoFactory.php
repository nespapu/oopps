<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Config\DatabaseConfig;
use PDO;

final class PdoFactory
{
    public function create(DatabaseConfig $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config->host,
            $config->database,
            $config->charset
        );

        return new PDO(
            $dsn,
            $config->user,
            $config->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}