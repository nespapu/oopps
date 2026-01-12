<?php
namespace App\Infrastructure\Persistence;
use PDO;

final class ConexionBD {
    private static ?PDO $pdo = null;

    public static function obtener(): PDO {
        if (!self::$pdo) {
            $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }
}