<?php
declare(strict_types=1);

use App\App\AppWiring;
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

$rutas = (new AppWiring())->rutas();

$manejador = $rutas[$rutaCanonica] ?? null;
if($manejador === null) {
    http_response_code(404);
    echo "404 - Ruta no encontrada";
    exit;
}

$manejador();

?>