<?php
namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Domain\Auth\UsuarioRepository;
use App\Core\View;

final class LoginController {
    public function __construct (
        private readonly AuthService $authService,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly UsuarioRepository $usuarioRepositorio
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

        $usuario = $this->usuarioRepositorio->buscarPorNombre($nombre);

        if (!$usuario || $usuario->clave() !== $clave) {
            $this->flash->set('error', 'Usuario o contraseña incorrectos');
            $this->redirector->redirect('login');
        }
        
        $codigoOposicion = trim($usuario->codigoOposicion() ?? '');
        if ($codigoOposicion === '') {
            $this->flash->set('error', 'No tienes una oposición activa configurada.');
            $this->redirector->redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario->nombre();
        $_SESSION['codigoOposicion'] = $codigoOposicion;
        
        $redireccion = $_SESSION['siguiente_url'] ?? 'panel-control-ejercicios';
        unset($_SESSION['siguiente_url']);
        $this->redirector->redirect($redireccion);               
    }

    public function salir () : void {
        $this->authService->logout();
    }
}
?>