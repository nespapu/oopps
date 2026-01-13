<?php
namespace App\Helpers;

use App\Domain\Auth\ContextoUsuario;
use App\Helpers\Http;
use App\Helpers\Router;

final class Auth {
    public static function requiereLogin(): void {
        if (!self::hayUsuarioLogueado()) {
            
            // Guardar ruta para redirigir al usuario si necesita autenticación previa
            if (empty($_SESSION['siguiente_url'])) {
                $_SESSION['siguiente_url'] = Router::obtenerRuta();
            }

            Http::redirigir('login');
            exit;
        }
    }

    public static function requiereContextoOposicion(): void
    {
        self::requiereLogin();

        $codigoOposicion = self::codigoOposicion();
        if ($codigoOposicion === null || $codigoOposicion === '') {
            Flash::set('error', 'No tienes una oposición activa configurada.');
            Http::redirigir('login');
            exit;
        }
    }

    public static function hayUsuarioLogueado(): bool {
        return !empty($_SESSION['usuario']);
    }

    public static function usuario(): ?string {
        return $_SESSION['usuario'] ?? null;
    }

    public static function codigoOposicion(): ?string {
        return ($_SESSION['codigoOposicion'] ?? null);
    }

    public static function contextoUsuario(): ContextoUsuario {
        self::requiereContextoOposicion();

        return new ContextoUsuario(
            (string) self::usuario(),
            (string) self::codigoOposicion()
        );
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        session_start();
        session_regenerate_id(true);

        Http::redirigir('login');
    }
}
?>