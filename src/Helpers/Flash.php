<?php
namespace App\Helpers;

final class Flash {
    public static function set(string $clave, string $mensaje): void {
        $_SESSION['_flash'][$clave] = $mensaje;
    }
    public static function get(string $clave): ?string {
        if (!isset($_SESSION['_flash'][$clave])) return null;
        $msg = $_SESSION['_flash'][$clave];
        unset($_SESSION['_flash'][$clave]);
        return $msg;
    }
}
?>