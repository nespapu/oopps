<?php
namespace App\Models;
use App\Database\Connection;

final class Oposicion {
    public static function buscarPorUsuario ($usuario) : array {
        $pdo = Connection::get();
        $sentencia = $pdo->prepare("SELECT o.* FROM oposicion o INNER JOIN usuario_estudiar_oposicion ueo ON o.codigo = ueo.codigo_oposicion WHERE ueo.nombre_usuario = :usuario");
        $sentencia->execute(['usuario' => $usuario]);
        $filas = $sentencia->fetchAll(\PDO::FETCH_ASSOC);
        return $filas;
    }
}
?>