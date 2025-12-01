<?php
namespace App\Helpers;

use App\Helpers\Http;
use App\Helpers\Router;

final class Auth {
    public static function requiereLogin(): void {
        if (!self::hayUsuarioLogueado()) {
            
            // Guardar ruta para redirigir al usuario si necesita autenticación previa
            if (empty($_SESSION['siguiente_url'])) {
                $_SESSION['siguiente_url'] = Router::obtenerRuta();
            }

            Http::redirigir('login/formulario');
            exit;
        }
    }

    public static function hayUsuarioLogueado(): bool {
        return !empty($_SESSION['usuario']);
    }

    public static function usuario(): ?string {
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