<?php

declare(strict_types=1);

namespace App\Application\Http;

interface HttpMethodGuard
{
    public function onlyGet(callable $handler): callable;

    public function onlyPost(callable $handler): callable;

    public function byMethod(?callable $get, ?callable $post): callable;

    public function onlyIf(callable $handler, bool $allowed): callable;
}
