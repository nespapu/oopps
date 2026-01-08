<?php
namespace App\Helpers;

final class Http {
    public static function redirigir(string $ruta, int $estado = 303): void {
        $url = Router::url($ruta);
        header('Location: ' . $url, true, $estado);
        exit;
    }
}