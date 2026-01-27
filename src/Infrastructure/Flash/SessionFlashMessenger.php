<?php
namespace App\Infrastructure\Flash;

use App\Application\Flash\FlashMessenger;

final class SessionFlashMessenger implements FlashMessenger {
    public function set(string $clave, string $mensaje): void {
        $_SESSION['_flash'][$clave] = $mensaje;
    }

    public function get(string $clave): ?string {
        if (!isset($_SESSION['_flash'][$clave])) {
            return null;
        }

        $msj = $_SESSION['_flash'][$clave];
        unset($_SESSION['_flash'][$clave]);

        return $msj;
    }
}
?>