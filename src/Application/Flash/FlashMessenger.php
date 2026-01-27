<?php
namespace App\Application\Flash;

interface FlashMessenger {
    public function set(string $clave, string $mensaje): void;
    public function get(string $clave): ?string;
}
?>