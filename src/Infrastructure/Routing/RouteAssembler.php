<?php
declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Http\HttpMethodGuard;

final class RouteAssembler
{
    public function __construct(
        private readonly HttpMethodGuard $httpMethodGuard
    ) {}

    /**
     * Transforma RouteDefinitions en el formato final de AppRoutes,
     * aplicando el guard de método sin que los módulos lo sepan.
     *
     * @return array<string, \Closure> map path => handler
     */
    public function assemble(RouteCollection $collection): array
    {
        $byPath = [];

        foreach ($collection->all() as $route) {
            $byPath[$route->path][] = $route;
        }

        $map = [];

        foreach ($byPath as $path => $routes) {
            $get  = null;
            $post = null;

            foreach ($routes as $route) {
                if ($route->method === HttpMethod::GET) {
                    $get = $route->handler;
                } elseif ($route->method === HttpMethod::POST) {
                    $post = $route->handler;
                } else {
                    throw new \LogicException("Unsupported HTTP method for path: {$path}");
                }
            }

            if ($get !== null || $post !== null) {
                $map[$path] = $this->httpMethodGuard->byMethod($get, $post);
                continue;
            }

            throw new \LogicException("No handlers found for path: {$path}");
        }

        return $map;
    }
}
