<?php
declare(strict_types=1);

namespace App\App\Http;

use App\Application\Http\RequestContext;
use App\Application\Routing\RouteCanonicalizer;
use Throwable;

final class AppKernel
{
    public function __construct(
        private readonly AppRoutes $appRoutes,
        private readonly RequestContext $requestContext,
        private readonly RouteCanonicalizer $routeCanonicalizer
    ) {}

    public function handle(): void
    {
        $env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';
        $route  = $this->requestContext->path();

        if (str_starts_with($route, 'dev/') && $env !== 'dev') {
            http_response_code(404);
            return;
        }

        [$canonicRoute, $_] = $this->routeCanonicalizer->canonicalize($route, $this->appRoutes);

        if (!$this->appRoutes->has($canonicRoute)) {
            http_response_code(404);
            echo "404 - Ruta no encontrada";
            return;
        }

        try {
            $handler = $this->appRoutes->get($canonicRoute);
            $handler();
        } catch (Throwable $e) {
            if ($env === 'dev') {
                throw $e;
            }
            http_response_code(500);
            echo "500 - Error interno";
        }
    }
}
