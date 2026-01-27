<?php
namespace App\Controllers;

use App\Application\Flash\FlashMessenger;
use App\Core\View;
use App\Helpers\Auth;
use App\Helpers\Http;
use App\Models\Usuario;

final class LoginController {
    public function __construct (
        private readonly FlashMessenger $flash
    ){}

    public function mostrar () : void {
        $error = $this->flash->get('error');
    
        View::render('login/index.php', [
            'error' => $error
        ]);
    }

    public function comprobar () : void {
        $nombre = $_POST['nombre'] ?? '';
        $clave  = $_POST['clave'] ?? '';

        $usuario = Usuario::buscarPorNombre($nombre);

        if (!$usuario || $usuario['clave'] !== $clave) {
            $this->flash->set('error', 'Usuario o contraseña incorrectos');
            Http::redirigir('login');
        }
        
        $codigoOposicion = trim((string)($usuario['codigo_oposicion'] ?? ''));
        if ($codigoOposicion === '') {
            $this->flash->set('error', 'No tienes una oposición activa configurada.');
            Http::redirigir('login');
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['codigoOposicion'] = $codigoOposicion;
        
        $redireccion = $_SESSION['siguiente_url'] ?? 'panel-control-ejercicios';
        unset($_SESSION['siguiente_url']);
        Http::redirigir($redireccion);               
    }

    public function salir () : void {
        Auth::logout();
    }
}
?>