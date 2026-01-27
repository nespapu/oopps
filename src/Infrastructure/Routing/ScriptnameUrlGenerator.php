<?php
declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Routing\UrlGenerator;

final class ScriptNameUrlGenerator implements UrlGenerator
{
    public function to(string $route = ''): string
    {
        $route = trim($route, '/');

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/'); // e.g. /oopps/public
        if ($basePath === '' || $basePath === '.') {
            $basePath = '';
        }

        if ($route === '') {
            return $basePath !== '' ? $basePath . '/' : '/';
        }

        return $basePath !== '' ? $basePath . '/' . $route : '/' . $route;
    }
}
?>