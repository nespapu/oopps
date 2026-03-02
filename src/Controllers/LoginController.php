<?php
namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Session\SessionStore;
use App\Domain\Auth\UserRepository;
use App\Core\View;

final class LoginController {
    public function __construct (
        private readonly AuthService $authService,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly SessionStore $sessionStore,
        private readonly UserRepository $usuarioRepositorio
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

        $usuario = $this->usuarioRepositorio->findByName($nombre);

        if (!$usuario || $usuario->password() !== $clave) {
            $this->flash->set('error', 'Usuario o contraseña incorrectos');
            $this->redirector->redirect('login');
        }
        
        $codigoOposicion = trim($usuario->oppositionCode() ?? '');
        if ($codigoOposicion === '') {
            $this->flash->set('error', 'No tienes una oposición activa configurada.');
            $this->redirector->redirect('login');
        }

        $this->sessionStore->startIfNeeded();
        $this->sessionStore->regenerateId(true);

        $this->sessionStore->setString('username', $usuario->name());
        $this->sessionStore->setString('opposition_code', $codigoOposicion);
        
        $redirectTo = trim($this->sessionStore->getString('next_url') ?? '');
        if ($redirectTo === '') {
            $redirectTo = 'panel-control-ejercicios';
        }

        // "clear" the one-time redirect
        $this->sessionStore->setString('next_url', '');

        $this->redirector->redirect($redirectTo);              
    }

    public function salir () : void {
        $this->authService->logout();
    }
}
?>