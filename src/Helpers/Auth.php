<?php
namespace App\Helpers;

use App\Helpers\Http;

final class Auth {
    public static function requiereLogin(): void {
        if (empty($_SESSION['usuario'])) {
            Http::redirigir('login/formulario');
        }
    }

    public static function userId(): ?int {
        return $_SESSION['usuario'] ?? null;
    }

    public static function logout(): void {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        Http::redirigir('login/formulario');
    }
}
?>