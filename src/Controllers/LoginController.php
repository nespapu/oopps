<?php
namespace App\Controllers;

use App\Core\View;
use App\Helpers\Auth;
use App\Models\Usuario;
use App\Helpers\Http;
use App\Helpers\Flash;

final class LoginController {
    public function mostrar () : void {
        $error = Flash::get('error');
    
        View::render('login/index.php', [
            'error' => $error
        ]);
    }

    public function comprobar () : void {
        $nombre = $_POST['nombre'] ?? '';
        $clave  = $_POST['clave'] ?? '';

        $usuario = Usuario::buscarPorNombre($nombre);

        if (!$usuario || $usuario['clave'] !== $clave) {
            Flash::set('error', 'Usuario o contraseña incorrectos');
            Http::redirigir('login');
        }
        
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario['nombre'];
        
        $redireccion = $_SESSION['siguiente_url'] ?? 'panel-control-ejercicios';
        unset($_SESSION['siguiente_url']);
        Http::redirigir($redireccion);               
    }

    public function salir () : void {
        Auth::logout();
    }
}
?>