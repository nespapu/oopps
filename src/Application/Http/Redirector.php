<?php

declare(strict_types=1);

namespace App\Application\Http;

interface Redirector
{
    public function redirect(string $route, int $status = 303): void;
}
