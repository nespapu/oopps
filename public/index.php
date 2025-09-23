<?php
declare(strict_types=1);

use App\Helpers\Http;
use App\Helpers\Auth;

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

$rutaBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); 
$rutaPeticion = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ruta = trim(substr($rutaPeticion, strlen($rutaBase)), '/'); 

if ($ruta === '') {
    $ruta = 'login/formulario';
}

switch ($ruta) {
    case 'login/formulario':
        (new App\Controllers\LoginController())->mostrar();
        break;
    case 'login/comprobar':
        (new App\Controllers\LoginController())->comprobar();
        break;
    case 'login/error':
        (new App\Controllers\LoginController())->error();
        break;
    case 'login/salir':
        (new App\Controllers\LoginController())->salir();
        break;
    case 'oposicion/formulario':
        Auth::requiereLogin();
        (new App\Controllers\OposicionController())->mostrar();
        break;
    default:
        http_response_code(404);
        echo "404 - Ruta no encontrada";
}
?>