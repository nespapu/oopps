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
        $nombre = $_POST["nombre"];
        $clave = $_POST["clave"];

        $usuario = Usuario::buscarPorNombre($nombre);

        if ($usuario) {
            if ($usuario["clave"] != $clave) {
                Flash::set('error', 'La contraseña no es correcta');
                Http::redirigir("login/formulario");
            }
        } else {
            Flash::set('error', 'El nombre de usuario no existe');
            Http::redirigir("login/formulario");
        }
        
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario['nombre'];
        
        $redireccion = $_SESSION['siguiente_url'] ?? 'panel-control-ejercicios';
        unset($_SESSION['siguiente_url']);
        Http::redirigir($redireccion);               
        
        exit;
    }

    public function error () {
        $titulo = "Login";
        $error = Flash::get('error'); 

        ob_start();
        require __DIR__.'/../../views/login/error.php';
        $contenido = ob_get_clean();
        
        require __DIR__.'/../../views/plantilla.php';
    }

    public function salir () {
        Auth::logout();
    }
}
?>