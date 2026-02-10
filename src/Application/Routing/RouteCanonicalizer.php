<?php
declare(strict_types=1);

namespace App\Application\Routing;

final class RouteCanonicalizer
{
    /**
     * @return array{0:string,1:array<string,string>} [canonicalPattern, params]
     */
    public function canonicalize(string $path, RoutePatternProvider $provider): array
    {
        foreach ($provider->patterns() as $pattern) {
            $params = $this->match($pattern, $path);
            if ($params !== null) {
                return [$pattern, $params];
            }
        }

        return [$path, []];
    }

    /** @return array<string,string>|null */
    private function match(string $pattern, string $path): ?array
    {
        $paramNames = [];

        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];
                return '([^/]+)';
            },
            $pattern
        );

        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        array_shift($matches);

        $params = [];
        foreach ($paramNames as $i => $name) {
            $params[$name] = $matches[$i] ?? '';
        }

        return $params;
    }
}
