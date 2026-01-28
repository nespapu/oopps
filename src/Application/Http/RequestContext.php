<?php
declare(strict_types=1);

namespace App\Application\Http;

interface RequestContext
{
    public function path(): string;
    public function method(): string;
    public function isGet(): bool;
    public function isPost(): bool;
}
?>