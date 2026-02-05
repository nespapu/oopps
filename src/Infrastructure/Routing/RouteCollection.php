<?php
declare(strict_types=1);

namespace App\Infrastructure\Routing;

final class RouteCollection
{
    /** @var RouteDefinition[] */
    private array $routes = [];

    public function add(RouteDefinition $route): void
    {
        $this->routes[] = $route;
    }

    /** @return RouteDefinition[] */
    public function all(): array
    {
        return $this->routes;
    }

    public function merge(RouteCollection $other): void
    {
        foreach ($other->all() as $route) {
            $this->add($route);
        }
    }
}
