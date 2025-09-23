<?php
namespace App\Models;
use App\Database\Connection;

final class Usuario {
    public static function buscarPorNombre ($nombre) : ?array {
        $pdo = Connection::get();
        $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE nombre = :nombre");
        $sentencia->execute(['nombre' => $nombre]);
        $fila = $sentencia->fetch();
        return $fila ?: null;
    }
}
?>