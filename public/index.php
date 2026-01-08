<?php
declare(strict_types=1);

use App\Helpers\Router;
use App\Core\CanonizadorRuta;
use App\Core\Routes\RutasApp;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
  'lifetime' => 0,              // hasta cerrar navegador
  'path'     => '/', 
  'domain'   => '',             // déjalo vacío salvo que tengas subdominios
  'secure'   => $isHttps,       // true si sirves por HTTPS
  'httponly' => true,           // no accesible desde JS
  'samesite' => 'Lax',          // 'Lax' recomendado para PRG; 'Strict' si no hay interacciones cross-site
]);

ini_set('session.use_only_cookies', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$ruta = Router::obtenerRuta();
$entorno = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';

if (str_starts_with($ruta, 'dev/') && $entorno !== 'dev') {
    http_response_code(404);
    exit;
}

[$rutaCanonica, $params] = CanonizadorRuta::canonizar($ruta, RutasApp::patrones());

switch ($rutaCanonica) {
    case 'login':
        $controlador = new App\Controllers\LoginController();
        if (Router::esGet()) {
            $controlador->mostrar();
        } elseif (Router::esPost()) {
            $controlador->comprobar();
        } else {
            http_response_code(405);
        }
        break;
    case 'login/salir':
        (new App\Controllers\LoginController())->salir();
        break;
    case 'panel-control-ejercicios':
        (new App\Controllers\PanelControlEjerciciosController())->mostrar();
        break;
    case RutasCuantoSabesTema::CONFIG:
        if (!Router::esGet()) {
            http_response_code(405);
            break;
        }
        echo "TODO: Configuración ejercicio Cuánto sabes del tema";
        break;
    case RutasCuantoSabesTema::INICIO:
        if (!Router::esPost()) {
            http_response_code(405);
            break;
        }
        echo "TODO: Iniciar ejercicio Cuánto sabes del tema";
    case RutasCuantoSabesTema::PASO_TITULO:
        if (!Router::esGet()) {
            http_response_code(405);
            break;
        }
        $sesionId = $params['sesionId'] ?? '';
        echo "TODO: Paso Título, sesionId=" . htmlspecialchars($sesionId, ENT_QUOTES, 'UTF-8');
        break;
    case RutasCuantoSabesTema::EVAL_TITULO:
        if (!Router::esPost()) {
            http_response_code(405);
            break;
        }
        $sesionId = $params['sesionId'] ?? '';
        echo "TODO: Evaluar paso Título, sesionId=" . htmlspecialchars($sesionId, ENT_QUOTES, 'UTF-8');
        break;
    case RutasDevSesionEjercicio::BASE:
        if (!Router::esGet()) {
            http_response_code(405);
            break;
        }
        $almacen = new \App\Infrastructure\Session\AlmacenSesionEjercicio();
        (new \App\Controllers\Dev\DevSesionEjercicioController($almacen))->mostrar();
        break;
    // ======================
    // DEV ROUTES (dev-only)
    // ======================
    case RutasDevSesionEjercicio::SIGUIENTE:
        if (!Router::esPost()) {
            http_response_code(405);
            break;
        }
        $almacen = new \App\Infrastructure\Session\AlmacenSesionEjercicio();
        (new \App\Controllers\Dev\DevSesionEjercicioController($almacen))->siguiente();
        break;
    case RutasDevSesionEjercicio::RESET:
        if (!Router::esPost()) {
            http_response_code(405);
            break;
        }
        $almacen = new \App\Infrastructure\Session\AlmacenSesionEjercicio();
        (new \App\Controllers\Dev\DevSesionEjercicioController($almacen))->reset();
        break;
    default:
        http_response_code(404);
        echo "404 - Ruta no encontrada";
}
?>