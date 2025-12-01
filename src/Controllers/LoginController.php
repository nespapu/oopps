<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Usuario;
use App\Helpers\Http;
use App\Helpers\Flash;

final class LoginController {
    public function mostrar () : void {
        // Datos vista
        $titulo = "Login";

        // Renderizar vista
        ob_start();
        require __DIR__.'/../../views/login/formulario.php';
        $contenido = ob_get_clean();

        // Cargar plantilla
        require __DIR__.'/../../views/plantilla.php';
    }

    public function comprobar () : void {
        $titulo = "Login";

        $nombre = $_POST["nombre"];
        $clave = $_POST["clave"];

        $usuario = Usuario::buscarPorNombre($nombre);

        if ($usuario) {
            if ($usuario["clave"] != $clave) {
                Flash::set('error', 'clave');
                Http::redirigir("login/error");
            }
        } else {
            Flash::set('error', 'nombre');
            Http::redirigir("login/error");
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