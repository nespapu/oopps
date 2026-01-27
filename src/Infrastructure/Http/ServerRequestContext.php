<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Http\RequestContext;

final class ServerRequestContext implements RequestContext
{
    public function __construct(
        private string $defaultRoute = 'panel-control-ejercicios'
    ) {}

    public function path(): string
    {
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        $path = (string) parse_url($uri, PHP_URL_PATH);

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($basePath !== '' && $basePath !== '.' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        $route = trim($path, '/');

        return $route === '' ? $this->defaultRoute : $route;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }
}
