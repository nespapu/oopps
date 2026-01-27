<?php
declare(strict_types=1);

namespace App\Application\Routing;

interface UrlGenerator
{
    public function to(string $route = ''): string;
}
?>