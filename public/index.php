<?php
declare(strict_types=1);

use App\App\AppWiring;
use App\Core\CanonizadorRuta;
use App\Core\Routes\RutasApp;
use App\Helpers\Router;

require __DIR__ . '/../vendor/autoload.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

ini_set('session.use_only_cookies', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$entorno = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';

$ruta = Router::obtenerRuta();

if (str_starts_with($ruta, 'dev/') && $entorno !== 'dev') {
    http_response_code(404);
    exit;
}

[$rutaCanonica, $params] = CanonizadorRuta::canonizar($ruta, RutasApp::patrones());

$rutas = (new AppWiring())->rutas();
$manejador = $rutas[$rutaCanonica] ?? null;

if ($manejador === null) {
    http_response_code(404);
    echo "404 - Ruta no encontrada";
    exit;
}

try {
    $manejador();
} catch (Throwable $e) {
    if ($entorno === 'dev') {
        throw $e;
    }
    http_response_code(500);
    echo "500 - Error interno";
}

?>