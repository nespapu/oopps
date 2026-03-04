<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

interface DatabaseConfigProvider
{
    public function get(): DatabaseConfig;
}