<?php

declare(strict_types=1);

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

        View::render('login/form', [
            'error' => $error,
        ]);
    }

    public function authenticate(): void
    {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $user = $this->userRepository->findByName($username);

        if ($user === null || $user->password() !== $password) {
            $this->flash->set('error', 'Usuario o contraseña incorrectos');
            $this->redirector->redirect('login');
        }

        $oppositionCode = trim((string) ($user->oppositionCode() ?? ''));
        if ($oppositionCode === '') {
            $this->flash->set('error', 'No tienes una oposición activa configurada.');
            $this->redirector->redirect('login');
        }

        $this->sessionStore->startIfNeeded();
        $this->sessionStore->regenerateId(true);

        $this->sessionStore->setString('username', $user->name());
        $this->sessionStore->setString('opposition_code', $oppositionCode);

        $redirectTo = trim((string) ($this->sessionStore->getString('next_url') ?? ''));
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