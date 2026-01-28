<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Http\HttpMethodGuard;
use App\Application\Http\RequestContext;

final class DefaultHttpMethodGuard implements HttpMethodGuard
{
    public function __construct(
        private readonly RequestContext $requestContext
    ) {}

    public function onlyGet(callable $handler): callable
    {
        return function () use ($handler): void {
            if (!$this->requestContext->isGet()) {
                http_response_code(405);
                return;
            }

            $handler();
        };
    }

    public function onlyPost(callable $handler): callable
    {
        return function () use ($handler): void {
            if (!$this->requestContext->isPost()) {
                http_response_code(405);
                return;
            }

            $handler();
        };
    }

    public function byMethod(?callable $get, ?callable $post): callable
    {
        return function () use ($get, $post): void {
            if ($this->requestContext->isGet()) {
                if ($get !== null) { $get(); return; }
            }

            if ($this->requestContext->isPost()) {
                if ($post !== null) { $post(); return; }
            }

            http_response_code(405);
        };
    }

    public function onlyIf(callable $handler, bool $allowed): callable
    {
        return function () use ($handler, $allowed): void {
            if (!$allowed) {
                http_response_code(405);
                return;
            }

            $handler();
        };
    }
}
