<?php
namespace App\Helpers;

final class Http {
    public static function redirigir(string $ruta, int $estado = 303): void {
        $rutaBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $ruta = $rutaBase . '/' . ltrim($ruta, '/');
        header('Location: ' . $ruta, true, $estado);
        exit;
    }
}