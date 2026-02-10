<?php
declare(strict_types=1);

namespace App\Application\Routing;

final class RouteUrlGenerator
{
    /** @param array<string, string> $params */
    public function generate(string $pattern, array $params): string
    {
        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', rawurlencode($value), $pattern);
        }

        return $pattern;
    }
}
