<?php

namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Session\SessionStore;
use App\Core\View;
use App\Domain\Auth\UserRepository;

final class LoginController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly SessionStore $sessionStore,
        private readonly UserRepository $userRepository
    ) {}

    public function show(): void
    {
        $error = $this->flash->get('error');

        View::render('login/index', [
            'error' => $error,
        ]);
    }

    public function authenticate(): void
    {
        $username = $_POST['nombre'] ?? '';
        $password = $_POST['clave'] ?? '';

        $user = $this->userRepository->findByName($username);

        if (!$user || $user->password() !== $password) {
            $this->flash->set('error', 'Usuario o contraseña incorrectos');
            $this->redirector->redirect('login');
        }

        $oppositionCode = trim($user->oppositionCode() ?? '');
        if ($oppositionCode === '') {
            $this->flash->set('error', 'No tienes una oposición activa configurada.');
            $this->redirector->redirect('login');
        }

        $this->sessionStore->startIfNeeded();
        $this->sessionStore->regenerateId(true);

        $this->sessionStore->setString('username', $user->name());
        $this->sessionStore->setString('opposition_code', $oppositionCode);

        $redirectTo = trim($this->sessionStore->getString('next_url') ?? '');
        if ($redirectTo === '') {
            $redirectTo = 'panel-control-ejercicios';
        }

        $this->sessionStore->remove('next_url');

        $this->redirector->redirect($redirectTo);
    }

    public function logout(): void
    {
        $this->authService->logout();
    }
}