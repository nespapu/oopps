<?php
namespace App\Helpers;

class Router {
    public static function obtenerRuta(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $rutaBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        if (strpos($path, $rutaBase) === 0) {
            $path = substr($path, strlen($rutaBase));
        }

        $ruta = trim($path, '/');
        return $ruta === '' ? 'panel-control-ejercicios' : $ruta;
    }

    public static function obtenerMetodo(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function esPost(): bool {
        return self::obtenerMetodo() === 'POST';
    }

    public static function esGet(): bool {
        return self::obtenerMetodo() === 'GET';
    }
}
?>