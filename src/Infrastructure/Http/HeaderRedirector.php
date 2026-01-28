<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;

final class HeaderRedirector implements Redirector
{
    public function __construct(
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function redirect(string $route, int $status = 303): void
    {
        $url = $this->urlGenerator->to($route);

        header('Location: ' . $url, true, $status);
        exit;
    }
}
