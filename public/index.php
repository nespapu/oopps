<?php
declare(strict_types=1);

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
    case 'oposicion/formulario':
        (new App\Controllers\OposicionController())->mostrar();
        break;
    default:
        http_response_code(404);
        echo "404 - Ruta no encontrada";
}
?>