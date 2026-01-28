<?php
declare(strict_types=1);

namespace App\App\Http;

final class AppRoutes
{
    /** @var array<string, callable> */
    private array $routes;

    /**
     * @param array<string, callable> $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function has(string $route): bool
    {
        return isset($this->routes[$route]);
    }

    public function get(string $route): callable
    {
        return $this->routes[$route];
    }
}
