<?php
declare(strict_types=1);

namespace App\Application\Http;

interface RequestContext
{
    public function path(): string;     // normalized route/path
    public function method(): string;   // GET, POST, ...
    public function isGet(): bool;
    public function isPost(): bool;
}
?>