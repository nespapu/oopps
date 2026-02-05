<?php
declare(strict_types=1);

namespace App\Infrastructure\Routing;

final class RouteDefinition
{
    public function __construct(
        public readonly string $path,
        public readonly HttpMethod $method,
        public readonly \Closure $handler
    ) {}
}
