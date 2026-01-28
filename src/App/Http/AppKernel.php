<?php
declare(strict_types=1);

namespace App\App\Http;

use App\Application\Http\RequestContext;
use App\Core\CanonizadorRuta;
use App\Core\Routes\RutasApp;
use Throwable;

final class AppKernel
{
    public function __construct(
        private readonly AppRoutes $appRoutes,
        private readonly RequestContext $requestContext
    ) {}

    public function handle(): void
    {
        $env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';
        $route  = $this->requestContext->path();

        if (str_starts_with($route, 'dev/') && $env !== 'dev') {
            http_response_code(404);
            return;
        }

        [$canonicRoute, $_] = CanonizadorRuta::canonizar($route, RutasApp::patrones());

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
