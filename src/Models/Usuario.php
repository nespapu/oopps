<?php
namespace App\Models;

use App\Infrastructure\Persistence\ConexionBD;

final class Usuario {
    public static function buscarPorNombre ($nombre) : ?array {
        $pdo = ConexionBD::obtener();
        $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE nombre = :nombre");
        $sentencia->execute(['nombre' => $nombre]);
        $fila = $sentencia->fetch();
        return $fila ?: null;
    }
}
?>